/* eslint-disable no-restricted-globals */

let pyodideReadyPromise = null;
let pyodide = null;

function resetPyodide() {
  pyodide = null;
  pyodideReadyPromise = null;
}

async function ensurePyodide() {
  if (pyodide) return pyodide;
  if (!pyodideReadyPromise) {
    pyodideReadyPromise = (async () => {
      // Pyodide loads from CDN. First load can take a while.
      importScripts('https://cdn.jsdelivr.net/pyodide/v0.25.1/full/pyodide.js');
      // eslint-disable-next-line no-undef
      pyodide = await loadPyodide({
        indexURL: 'https://cdn.jsdelivr.net/pyodide/v0.25.1/full/',
      });
      return pyodide;
    })();
  }
  return await pyodideReadyPromise;
}

function deepEqual(a, b) {
  if (a === b) return true;
  if (typeof a !== typeof b) return false;
  if (a === null || b === null) return a === b;

  if (Array.isArray(a) && Array.isArray(b)) {
    if (a.length !== b.length) return false;
    for (let i = 0; i < a.length; i++) {
      if (!deepEqual(a[i], b[i])) return false;
    }
    return true;
  }

  if (typeof a === 'object' && typeof b === 'object') {
    const ak = Object.keys(a).sort();
    const bk = Object.keys(b).sort();
    if (ak.length !== bk.length) return false;
    for (let i = 0; i < ak.length; i++) {
      if (ak[i] !== bk[i]) return false;
      if (!deepEqual(a[ak[i]], b[bk[i]])) return false;
    }
    return true;
  }

  return false;
}

function canonicalize(value) {
  if (Array.isArray(value)) {
    const normalized = value.map(canonicalize);
    normalized.sort((a, b) => {
      const sa = JSON.stringify(a);
      const sb = JSON.stringify(b);
      return sa > sb ? 1 : sa < sb ? -1 : 0;
    });
    return normalized;
  }

  if (value && typeof value === 'object') {
    const normalized = {};
    for (const key of Object.keys(value).sort()) {
      normalized[key] = canonicalize(value[key]);
    }
    return normalized;
  }

  return value;
}

function unorderedArrayEqual(a, b) {
  if (!Array.isArray(a) || !Array.isArray(b)) return false;
  return deepEqual(canonicalize(a), canonicalize(b));
}

function compare(comparator, actual, expected) {
  switch (comparator) {
    case 'strictEqual':
      return actual === expected;
    case 'deepEqual':
      return deepEqual(actual, expected);
    case 'unorderedArray':
      return unorderedArrayEqual(actual, expected);
    default:
      return deepEqual(actual, expected);
  }
}

function toJs(value) {
  // Convert PyProxy / Python objects into JS primitives when possible.
  if (value && typeof value.toJs === 'function') {
    try {
      return value.toJs({ dict_converter: Object.fromEntries });
    } catch {
      // fallthrough
    }
  }
  return value;
}

function safePyRun(py, code) {
  try { return py.runPython(code); } catch { return ''; }
}

async function runPythonTests({ code, entryName, tests }) {
  const py = await ensurePyodide();

  // Redirect Python stdout/stderr to capture print() output
  await py.runPythonAsync(`
import sys, io
__tip_stdout = io.StringIO()
sys.stdout = __tip_stdout
sys.stderr = __tip_stdout
`);

  // Define the user's function in globals.
  try {
    await py.runPythonAsync(code);
  } catch (e) {
    const stdout = safePyRun(py, '__tip_stdout.getvalue()') || '';
    safePyRun(py, 'sys.stdout = sys.__stdout__; sys.stderr = sys.__stderr__');
    return {
      ok: false,
      error: `Syntax error: ${e.message}`,
      stdout,
      results: [],
    };
  }

  const fn = py.globals.get(entryName);
  if (!fn) {
    const stdout = safePyRun(py, '__tip_stdout.getvalue()') || '';
    safePyRun(py, 'sys.stdout = sys.__stdout__; sys.stderr = sys.__stderr__');
    return {
      ok: false,
      error: `Could not find a function named "${entryName}".`,
      stdout,
      results: [],
    };
  }

  const results = [];
  let passed = 0;

  for (let i = 0; i < tests.length; i++) {
    const t = tests[i];
    const args = Array.isArray(t.args) ? t.args : [];
    // Reset stdout capture for each test
    safePyRun(py, '__tip_stdout.truncate(0); __tip_stdout.seek(0)');
    try {
      const testStartedAt = performance.now();
      const pyRes = fn(...args);
      const actual = toJs(pyRes);
      const elapsedMs = performance.now() - testStartedAt;
      const stdout = safePyRun(py, '__tip_stdout.getvalue()') || '';
      const ok = compare(t.comparator, actual, t.expected);
      if (ok) passed++;
      results.push({ index: i, ok, args, expected: t.expected, actual, stdout, elapsedMs });
    } catch (e) {
      const stdout = safePyRun(py, '__tip_stdout.getvalue()') || '';
      results.push({
        index: i,
        ok: false,
        args,
        expected: t.expected,
        actual: null,
        runtimeError: String(e && e.message ? e.message : e),
        stdout,
        elapsedMs: 0,
      });
    }
  }

  // Restore stdout
  safePyRun(py, 'sys.stdout = sys.__stdout__; sys.stderr = sys.__stderr__');
  try { fn.destroy && fn.destroy(); } catch {}

  return {
    ok: passed === tests.length,
    passed,
    total: tests.length,
    elapsedMs: results.reduce((sum, r) => sum + (r.elapsedMs || 0), 0),
    results,
  };
}

self.onmessage = async (ev) => {
  const msg = ev.data || {};
  if (msg.type === 'preload') {
    const startedAt = Date.now();
    try {
      await ensurePyodide();
      self.postMessage({
        type: 'preloadResult',
        requestId: msg.requestId,
        startedAt,
        finishedAt: Date.now(),
        ok: true,
      });
    } catch (e) {
      resetPyodide();
      self.postMessage({
        type: 'preloadResult',
        requestId: msg.requestId,
        startedAt,
        finishedAt: Date.now(),
        ok: false,
        error: String(e && e.message ? e.message : e),
      });
    }
    return;
  }

  if (msg.type !== 'run') return;

  const startedAt = Date.now();
  try {
    const payload = await runPythonTests(msg.payload || {});
    self.postMessage({
      type: 'result',
      requestId: msg.requestId,
      startedAt,
      finishedAt: Date.now(),
      payload,
    });
  } catch (e) {
    const isFatal = /fatally failed/.test(String(e));
    if (isFatal) resetPyodide();
    self.postMessage({
      type: 'result',
      requestId: msg.requestId,
      startedAt,
      finishedAt: Date.now(),
      payload: {
        ok: false,
        fatal: isFatal,
        error: isFatal
          ? 'Python runtime crashed. Click Run again to reload it.'
          : String(e && e.message ? e.message : e),
      },
    });
  }
};
