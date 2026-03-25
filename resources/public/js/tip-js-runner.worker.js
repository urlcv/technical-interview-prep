/* eslint-disable no-restricted-globals */

function safeStringify(value) {
  try {
    return JSON.stringify(value);
  } catch {
    return '"[unserializable]"';
  }
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
      const sa = safeStringify(a);
      const sb = safeStringify(b);
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

function runTests({ code, entryName, tests }) {
  // Capture console.log output from user code
  const _origLog = console.log;
  let _stdout = [];
  console.log = function() {
    _stdout.push(Array.from(arguments).map(a => {
      try { return typeof a === 'string' ? a : JSON.stringify(a); } catch { return String(a); }
    }).join(' '));
  };

  // Wrap code so we can access the requested entry point.
  // We intentionally evaluate in worker-global scope.
  // eslint-disable-next-line no-new-func
  let fn;
  try {
    fn = new Function(
      '"use strict";\n' +
        code +
        '\n;return (typeof ' +
        entryName +
        " !== 'undefined') ? " +
        entryName +
        " : null;"
    )();
  } catch (e) {
    console.log = _origLog;
    return {
      ok: false,
      error: `Syntax error: ${e.message}`,
      stdout: _stdout.join('\n'),
      results: [],
    };
  }

  if (typeof fn !== 'function') {
    console.log = _origLog;
    return {
      ok: false,
      error: `Could not find a function named "${entryName}".`,
      stdout: _stdout.join('\n'),
      results: [],
    };
  }

  const results = [];
  let passed = 0;

  for (let i = 0; i < tests.length; i++) {
    const t = tests[i];
    const args = Array.isArray(t.args) ? t.args : [];
    _stdout = [];
    try {
      const testStartedAt = performance.now();
      const actual = fn(...args);
      const elapsedMs = performance.now() - testStartedAt;
      const ok = compare(t.comparator, actual, t.expected);
      if (ok) passed++;
      results.push({
        index: i,
        ok,
        args,
        expected: t.expected,
        actual,
        stdout: _stdout.join('\n'),
        elapsedMs,
      });
    } catch (e) {
      const elapsedMs = 0;
      results.push({
        index: i,
        ok: false,
        args,
        expected: t.expected,
        actual: null,
        runtimeError: String(e && e.message ? e.message : e),
        stdout: _stdout.join('\n'),
        elapsedMs,
      });
    }
  }

  console.log = _origLog;

  return {
    ok: passed === tests.length,
    passed,
    total: tests.length,
    elapsedMs: results.reduce((sum, r) => sum + (r.elapsedMs || 0), 0),
    results,
  };
}

self.onmessage = (ev) => {
  const msg = ev.data || {};
  if (msg.type !== 'run') return;

  const startedAt = Date.now();
  try {
    const payload = runTests(msg.payload || {});
    self.postMessage({
      type: 'result',
      requestId: msg.requestId,
      startedAt,
      finishedAt: Date.now(),
      payload,
    });
  } catch (e) {
    self.postMessage({
      type: 'result',
      requestId: msg.requestId,
      startedAt,
      finishedAt: Date.now(),
      payload: {
        ok: false,
        error: String(e && e.message ? e.message : e),
        debug: safeStringify(e),
      },
    });
  }
};
