{{-- Technical Interview Prep — fully client-side Alpine.js tool --}}
<div x-data="techInterviewPrep()" x-init="init()" x-cloak
     @keydown.window="handleKey($event)" class="min-h-[80vh]">

{{-- Top Navigation --}}
<div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-6">
  <div class="flex items-center gap-2 flex-wrap">
    <button @click="go('landing')" class="px-3 py-1.5 rounded-lg text-sm font-medium"
      :class="view==='landing'?'bg-indigo-100 text-indigo-700':'text-gray-600 hover:bg-gray-100'">Home</button>
    <button @click="go('bank')" class="px-3 py-1.5 rounded-lg text-sm font-medium"
      :class="view==='bank'?'bg-indigo-100 text-indigo-700':'text-gray-600 hover:bg-gray-100'">Questions</button>
    <button @click="go('focusMap')" class="px-3 py-1.5 rounded-lg text-sm font-medium"
      :class="view==='focusMap'?'bg-indigo-100 text-indigo-700':'text-gray-600 hover:bg-gray-100'">Focus Map</button>
    <button @click="go('settings')" class="px-3 py-1.5 rounded-lg text-sm font-medium"
      :class="view==='settings'?'bg-indigo-100 text-indigo-700':'text-gray-600 hover:bg-gray-100'">Settings</button>
  </div>
  <div class="flex items-center gap-3 text-sm">
    <span class="inline-flex items-center gap-1 text-amber-600 font-semibold" x-show="user.streak.current>0">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"/></svg>
      <span x-text="user.streak.current + ' day' + (user.streak.current!==1?'s':'')"></span>
    </span>
    <span x-show="view==='practice'" x-transition class="text-gray-400 text-xs hidden sm:inline">Keyboard: <kbd class="px-1 py-0.5 bg-gray-100 border border-gray-200 rounded text-[10px] text-gray-500">H</kbd> <kbd class="px-1 py-0.5 bg-gray-100 border border-gray-200 rounded text-[10px] text-gray-500">N</kbd> <kbd class="px-1 py-0.5 bg-gray-100 border border-gray-200 rounded text-[10px] text-gray-500">S</kbd></span>
  </div>
</div>

{{-- ===== LANDING VIEW ===== --}}
<div x-show="view==='landing'" x-transition class="space-y-6">
  {{-- Bookmark resume --}}
  <template x-if="user.bookmark">
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center justify-between">
      <div>
        <div class="text-sm font-semibold text-indigo-800">Pick up where you left off</div>
        <div class="text-sm text-indigo-600" x-text="getQ(user.bookmark.questionId)?.title + ' — Step ' + (user.bookmark.step+1) + ' of 11'"></div>
      </div>
      <button @click="resumeBookmark()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Resume</button>
    </div>
  </template>

  {{-- Review queue --}}
  <template x-if="getReviewQueue().length > 0 && !user.bookmark">
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
      <div class="flex items-center justify-between mb-2">
        <div class="text-sm font-semibold text-emerald-800">Your review queue for today</div>
        <span class="text-xs text-emerald-600" x-text="getReviewQueue().length + ' question' + (getReviewQueue().length!==1?'s':'')"></span>
      </div>
      <div class="flex flex-wrap gap-2 mb-3">
        <template x-for="q in getReviewQueue().slice(0,5)" :key="q.id">
          <button @click="startPractice(q)" class="px-3 py-1.5 bg-white border border-emerald-200 rounded-lg text-sm text-emerald-700 hover:bg-emerald-100" x-text="q.title"></button>
        </template>
      </div>
      <button @click="startPractice(getReviewQueue()[0])" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">Start review</button>
    </div>
  </template>

  {{-- Session mode selector --}}
  <div>
    <h2 class="text-lg font-bold text-gray-900 mb-1">How much time do you have?</h2>
    <p class="text-sm text-gray-500 mb-4">Pick your available time and we'll serve the right session.</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <button @click="startEnergySession(10)" class="group relative bg-white border-2 border-gray-200 hover:border-sky-400 rounded-xl p-5 text-left transition-all">
        <div class="text-2xl font-bold text-gray-900">10 min</div>
        <div class="text-sm text-gray-500 mt-1">Quick drill — pattern recognition, complexity, or flashcards</div>
        <div class="absolute top-3 right-3 w-2 h-2 rounded-full bg-sky-400"></div>
      </button>
      <button @click="startEnergySession(30)" class="group relative bg-white border-2 border-gray-200 hover:border-amber-400 rounded-xl p-5 text-left transition-all">
        <div class="text-2xl font-bold text-gray-900">30 min</div>
        <div class="text-sm text-gray-500 mt-1">Full guided problem with explain-before-code and reflection</div>
        <div class="absolute top-3 right-3 w-2 h-2 rounded-full bg-amber-400"></div>
      </button>
      <button @click="startEnergySession(60)" class="group relative bg-white border-2 border-gray-200 hover:border-rose-400 rounded-xl p-5 text-left transition-all">
        <div class="text-2xl font-bold text-gray-900">60+ min</div>
        <div class="text-sm text-gray-500 mt-1">Mock interview or deep practice session with warm-up</div>
        <div class="absolute top-3 right-3 w-2 h-2 rounded-full bg-rose-400"></div>
      </button>
    </div>
  </div>

  {{-- Quick actions --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <button @click="startWarmUp()" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-indigo-300 transition-all">
      <div class="text-lg mb-1">&#9889;</div><div class="text-sm font-medium text-gray-700">Warm Up</div>
    </button>
    <button @click="go('patternDrill')" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-indigo-300 transition-all">
      <div class="text-lg mb-1">&#129513;</div><div class="text-sm font-medium text-gray-700">Pattern Drill</div>
    </button>
    <button @click="go('complexityDrill')" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-indigo-300 transition-all">
      <div class="text-lg mb-1">&#128202;</div><div class="text-sm font-medium text-gray-700">Complexity Drill</div>
    </button>
    <button @click="surpriseMe()" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-indigo-300 transition-all">
      <div class="text-lg mb-1">&#127922;</div><div class="text-sm font-medium text-gray-700">Surprise Me</div>
    </button>
  </div>

  {{-- Today's stats --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="todaySessions()"></div>
      <div class="text-xs text-gray-500 mt-1">Sessions today</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="totalSolved()"></div>
      <div class="text-xs text-gray-500 mt-1">Problems solved</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="user.streak.current"></div>
      <div class="text-xs text-gray-500 mt-1">Day streak</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="solveRate() + '%'"></div>
      <div class="text-xs text-gray-500 mt-1">Solve rate</div>
    </div>
  </div>
</div>

{{-- ===== QUESTION BANK VIEW ===== --}}
<div x-show="view==='bank'" x-transition class="space-y-4">
  <div class="flex flex-wrap gap-3 items-center">
    <select x-model="filters.category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
      <option value="">All topics</option>
      <template x-for="c in allCategories" :key="c"><option :value="c" x-text="c.replace(/-/g,' ')"></option></template>
    </select>
    <select x-model="filters.difficulty" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
      <option value="">All difficulties</option>
      <option value="easy">Easy</option><option value="medium">Medium</option><option value="hard">Hard</option>
    </select>
    <select x-model="filters.pattern" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
      <option value="">All patterns</option>
      <template x-for="p in allPatterns" :key="p"><option :value="p" x-text="p.replace(/-/g,' ')"></option></template>
    </select>
    <select x-model="filters.cognitiveLoad" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
      <option value="">Any difficulty</option>
      <option value="low">Light</option><option value="medium">Moderate</option><option value="high">Challenging</option>
    </select>
    <span class="text-sm text-gray-400" x-text="filteredQuestions().length + ' questions'"></span>
  </div>
  <div class="space-y-2">
    <template x-for="q in filteredQuestions()" :key="q.id">
      <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between hover:border-indigo-300 transition-all cursor-pointer" @click="startPractice(q)">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="font-semibold text-gray-900 text-sm" x-text="q.title"></span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
              :class="{'bg-green-100 text-green-700':q.difficulty==='easy','bg-amber-100 text-amber-700':q.difficulty==='medium','bg-red-100 text-red-700':q.difficulty==='hard'}"
              x-text="q.difficulty"></span>
            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600" x-text="q.cognitiveLoad === 'low' ? 'light' : q.cognitiveLoad === 'medium' ? 'moderate' : 'challenging'"></span>
          </div>
          <div class="text-xs text-gray-500 truncate" x-text="q.patterns.map(p=>p.replace(/-/g,' ')).join(', ')"></div>
        </div>
        <div class="flex items-center gap-3 ml-4">
          <template x-if="getAttempts(q.id).length > 0">
            <span class="text-xs px-2 py-1 rounded-full"
              :class="lastAttemptSolved(q.id)?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700'"
              x-text="lastAttemptSolved(q.id)?'Solved':'To revisit'"></span>
          </template>
          <span class="text-xs text-gray-400" x-text="q.recommendedTime + ' min'"></span>
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
      </div>
    </template>
  </div>
</div>

{{-- ===== GUIDED PRACTICE VIEW ===== --}}
<div x-show="view==='practice'" x-transition class="space-y-4">
  <template x-if="currentQuestion">
    <div class="space-y-4">
      {{-- Problem statement (collapsible) --}}
      <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <button @click="showProblem=!showProblem" class="w-full flex items-center justify-between px-5 py-3 text-left hover:bg-gray-50">
          <div class="flex items-center gap-3">
            <h3 class="font-bold text-gray-900" x-text="currentQuestion.title"></h3>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
              :class="{'bg-green-100 text-green-700':currentQuestion.difficulty==='easy','bg-amber-100 text-amber-700':currentQuestion.difficulty==='medium','bg-red-100 text-red-700':currentQuestion.difficulty==='hard'}"
              x-text="currentQuestion.difficulty"></span>
            <span class="text-xs text-gray-400" x-text="currentQuestion.patterns.map(p=>p.replace(/-/g,' ')).join(', ') + ' · ' + currentQuestion.recommendedTime + ' min'"></span>
          </div>
          <svg class="w-5 h-5 text-gray-400 transition-transform" :class="showProblem ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="showProblem" x-collapse class="px-5 pb-4 space-y-3 border-t border-gray-100">
          <p class="text-sm text-gray-700 whitespace-pre-line pt-3" x-text="currentQuestion.statement"></p>
          <div class="space-y-2">
            <template x-for="ex in currentQuestion.examples" :key="ex.input">
              <div class="bg-gray-50 rounded-lg p-3 text-xs font-mono">
                <div><span class="text-gray-500">Input:</span> <span x-text="ex.input"></span></div>
                <div><span class="text-gray-500">Output:</span> <span x-text="ex.output"></span></div>
                <div class="text-gray-500 mt-1" x-text="ex.explanation" x-show="ex.explanation"></div>
              </div>
            </template>
          </div>
          <div>
            <label class="text-xs font-medium text-gray-500 block mb-1">Your notes</label>
            <textarea x-model="stepData.notes" @input.debounce.500ms="saveBookmark()" rows="2" placeholder="Scratch pad — jot anything here"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-y"></textarea>
          </div>
        </div>
      </div>

      {{-- Main area: current step --}}
      <div>
        {{-- Interview context banner --}}
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 mb-4 flex items-start gap-3">
          <div class="shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
          </div>
          <div class="text-sm">
            <span class="font-medium text-indigo-900">Practice like a real interview.</span>
            <span class="text-indigo-700">Talk through each step as if an interviewer is listening. Type your reasoning — the goal is to practise explaining, not just solving.</span>
          </div>
        </div>

        {{-- Progress stepper --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 mb-4">
          <div class="flex items-center justify-center gap-1 overflow-x-auto pb-1">
            <template x-for="(s, i) in stepLabels" :key="i">
              <div class="flex items-center">
                <button @click="if(i <= currentStep) currentStep = i" class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium border-2 transition-all shrink-0"
                  :class="i < currentStep ? 'bg-indigo-600 border-indigo-600 text-white cursor-pointer hover:bg-indigo-700' : i === currentStep ? 'border-indigo-600 text-indigo-600' : 'border-gray-200 text-gray-400 cursor-default'"
                  x-text="i+1"></button>
                <div x-show="i < stepLabels.length-1" class="w-3 sm:w-5 h-0.5 mx-0.5" :class="i < currentStep ? 'bg-indigo-600' : 'bg-gray-200'"></div>
              </div>
            </template>
          </div>
          {{-- Timer bar --}}
          <div class="mt-2" x-show="practiceTimer.running">
            <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
              <span x-text="formatTime(practiceTimer.elapsed)"></span>
              <span class="font-medium" x-text="stepLabels[currentStep]"></span>
              <span x-text="formatTime(practiceTimer.total)"></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="h-1.5 rounded-full transition-all duration-1000"
                :class="timerPct() > 90 ? 'bg-red-400' : timerPct() > 70 ? 'bg-amber-400' : 'bg-indigo-400'"
                :style="'width:' + Math.min(100, timerPct()) + '%'"></div>
            </div>
          </div>
        </div>

        {{-- Stuck nudge --}}
        <div x-show="showStuckNudge" x-transition class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
          <p class="text-sm text-amber-800 mb-2">You've been on this step for a bit — want a nudge?</p>
          <div class="flex flex-wrap gap-2">
            <button @click="revealHint()" class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-sm hover:bg-amber-700">Give me a hint</button>
            <button @click="dismissStuckNudge('thinking')" class="px-3 py-1.5 bg-white border border-amber-300 rounded-lg text-sm text-amber-700 hover:bg-amber-50">I'm thinking</button>
            <button @click="skipStep()" class="px-3 py-1.5 bg-white border border-amber-300 rounded-lg text-sm text-amber-700 hover:bg-amber-50">Skip to next step</button>
          </div>
        </div>

        {{-- Step content area --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">

          {{-- Step 0: Restate the problem --}}
          <div x-show="currentStep===0">
            <div class="flex items-center justify-between mb-1">
              <h3 class="font-semibold text-gray-900">Step 1: Restate the problem</h3>
              <div class="flex gap-2">
                <button @click="stepData.useTemplates=true" class="text-xs px-3 py-1 rounded-full" :class="stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Template</button>
                <button @click="stepData.useTemplates=false" class="text-xs px-3 py-1 rounded-full" :class="!stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Freeform</button>
              </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">In an interview, you'd repeat the problem back to the interviewer to show you understand it. Write it in your own words below.</p>
            <template x-if="stepData.useTemplates">
              <div class="space-y-3">
                <div>
                  <label class="text-sm font-medium text-gray-700 block mb-1">This problem is asking me to...</label>
                  <textarea x-model="stepData.restate" @input.debounce.500ms="saveBookmark()" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700 block mb-1">The input is... and the expected output is...</label>
                  <textarea x-model="stepData.inputOutput" @input.debounce.500ms="saveBookmark()" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
              </div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.restate" @input.debounce.500ms="saveBookmark()" rows="4" placeholder="In my own words, this problem is about..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 1: Clarify assumptions --}}
          <div x-show="currentStep===1">
            <h3 class="font-semibold text-gray-900 mb-1">Step 2: Clarify assumptions</h3>
            <p class="text-sm text-gray-500 mb-4">Good candidates ask clarifying questions before diving in. What would you ask the interviewer? Think about edge cases, input ranges, and constraints.</p>
            <textarea x-model="stepData.assumptions" @input.debounce.500ms="saveBookmark()" rows="4"
              placeholder="e.g.&#10;- Can the input be empty?&#10;- Are there negative numbers?&#10;- Is the array sorted?&#10;- Can there be duplicates?"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
          </div>

          {{-- Step 2: Identify inputs/outputs/constraints --}}
          <div x-show="currentStep===2">
            <h3 class="font-semibold text-gray-900 mb-1">Step 3: Define inputs, outputs, and constraints</h3>
            <p class="text-sm text-gray-500 mb-4">Be explicit about what goes in and what comes out. This is how interviewers check you actually understand the problem before you code.</p>
            <div class="space-y-3">
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Inputs</label><textarea x-model="stepData.inputs" @input.debounce.500ms="saveBookmark()" rows="2" placeholder="e.g. An integer array and a target integer" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Outputs</label><textarea x-model="stepData.outputs" @input.debounce.500ms="saveBookmark()" rows="2" placeholder="e.g. Indices of the two numbers that add up to target" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Constraints</label><textarea x-model="stepData.constraints" @input.debounce.500ms="saveBookmark()" rows="2" placeholder="e.g. Exactly one solution exists, can't use same element twice" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
            </div>
          </div>

          {{-- Step 3: Describe brute force --}}
          <div x-show="currentStep===3">
            <div class="flex items-center justify-between mb-1">
              <h3 class="font-semibold text-gray-900">Step 4: Describe a brute force approach</h3>
              <div class="flex gap-2" x-show="currentStep===3">
                <button @click="stepData.useTemplates=true" class="text-xs px-3 py-1 rounded-full" :class="stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Template</button>
                <button @click="stepData.useTemplates=false" class="text-xs px-3 py-1 rounded-full" :class="!stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Freeform</button>
              </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">Start simple. Interviewers want to see you can think of any working solution before optimising. What's the most straightforward approach, even if it's slow?</p>
            <template x-if="stepData.useTemplates">
              <div class="space-y-3">
                <div><label class="text-sm font-medium text-gray-700 block mb-1">A brute force approach would be to... because...</label><textarea x-model="stepData.bruteForce" @input.debounce.500ms="saveBookmark()" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
                <div><label class="text-sm font-medium text-gray-700 block mb-1">Its time complexity is O(___) because...</label><textarea x-model="stepData.bruteForceTC" @input.debounce.500ms="saveBookmark()" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
              </div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.bruteForce" @input.debounce.500ms="saveBookmark()" rows="5" placeholder="The simplest approach would be..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 4: Discuss trade-offs --}}
          <div x-show="currentStep===4">
            <h3 class="font-semibold text-gray-900 mb-1">Step 5: Discuss trade-offs</h3>
            <p class="text-sm text-gray-500 mb-4">Interviewers love hearing you think about trade-offs. Compare your brute force to a better approach — what are you trading for the speed gain?</p>
            <textarea x-model="stepData.tradeoffs" @input.debounce.500ms="saveBookmark()" rows="4" placeholder="e.g. Brute force is O(n²) using nested loops. We can trade space for time by using a hash map to get O(n)..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
          </div>

          {{-- Step 5: Describe optimal approach --}}
          <div x-show="currentStep===5">
            <div class="flex items-center justify-between mb-1">
              <h3 class="font-semibold text-gray-900">Step 6: Describe your optimal approach</h3>
              <div class="flex gap-2">
                <button @click="stepData.useTemplates=true" class="text-xs px-3 py-1 rounded-full" :class="stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Template</button>
                <button @click="stepData.useTemplates=false" class="text-xs px-3 py-1 rounded-full" :class="!stepData.useTemplates?'bg-indigo-100 text-indigo-700':'bg-gray-100 text-gray-600'">Freeform</button>
              </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">Now explain your better solution. Name the technique or pattern you'd use and walk through how it works.</p>
            <template x-if="stepData.useTemplates">
              <div class="space-y-3">
                <div><label class="text-sm font-medium text-gray-700 block mb-1">The optimal approach uses [pattern] because...</label><textarea x-model="stepData.optimal" @input.debounce.500ms="saveBookmark()" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
              </div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.optimal" @input.debounce.500ms="saveBookmark()" rows="5" placeholder="A better approach would be..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 6: Time complexity --}}
          <div x-show="currentStep===6">
            <h3 class="font-semibold text-gray-900 mb-1">Step 7: State time complexity</h3>
            <p class="text-sm text-gray-500 mb-4">You'll always be asked this. State the Big-O and explain <strong>why</strong> — don't just say "O(n)", justify it.</p>
            <template x-if="stepData.useTemplates">
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Time complexity is O(___) because...</label><textarea x-model="stepData.timeComplexity" @input.debounce.500ms="saveBookmark()" rows="3" placeholder="e.g. O(n) — we iterate through the array once, doing O(1) hash lookups" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.timeComplexity" @input.debounce.500ms="saveBookmark()" rows="3" placeholder="Time complexity and why..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 7: Space complexity --}}
          <div x-show="currentStep===7">
            <h3 class="font-semibold text-gray-900 mb-1">Step 8: State space complexity</h3>
            <p class="text-sm text-gray-500 mb-4">How much extra memory does your solution use? If you used a hash map, array, or stack, mention it here.</p>
            <template x-if="stepData.useTemplates">
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Space complexity is O(___) because...</label><textarea x-model="stepData.spaceComplexity" @input.debounce.500ms="saveBookmark()" rows="3" placeholder="e.g. O(n) — we store up to n elements in the hash map" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.spaceComplexity" @input.debounce.500ms="saveBookmark()" rows="3" placeholder="Space complexity and why..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 8: List edge cases --}}
          <div x-show="currentStep===8">
            <h3 class="font-semibold text-gray-900 mb-1">Step 9: List edge cases</h3>
            <p class="text-sm text-gray-500 mb-4">Before writing code, think about inputs that could break your solution. Interviewers specifically check if you consider these.</p>
            <template x-if="stepData.useTemplates">
              <div><label class="text-sm font-medium text-gray-700 block mb-1">Edge cases to watch for:</label><textarea x-model="stepData.edgeCases" @input.debounce.500ms="saveBookmark()" rows="3" placeholder="e.g.&#10;- Empty array&#10;- Single element&#10;- All duplicates&#10;- Very large input" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea></div>
            </template>
            <template x-if="!stepData.useTemplates">
              <textarea x-model="stepData.edgeCases" @input.debounce.500ms="saveBookmark()" rows="4" placeholder="What inputs could break your solution?" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </template>
          </div>

          {{-- Step 9: Write solution --}}
          <div x-show="currentStep===9">
            <h3 class="font-semibold text-gray-900 mb-1">Step 10: Write your solution</h3>
            <p class="text-sm text-gray-500 mb-4">Now code it up. Use any language or pseudocode — the goal is to practise translating your approach into working logic.</p>
            <textarea x-model="stepData.solution" @input.debounce.500ms="saveBookmark()" rows="12" placeholder="// Write your solution here&#10;// Use any language or pseudocode&#10;&#10;function solve(input) {&#10;  &#10;}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
          </div>

          {{-- Step 10: Reflect --}}
          <div x-show="currentStep===10">
            <h3 class="font-semibold text-gray-900 mb-1">Step 11: Reflect on your attempt</h3>
            <p class="text-sm text-gray-500 mb-4">This is the most important step for learning. Be honest about what went well and what didn't — your future self will thank you.</p>
            <div class="space-y-4">
              <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">What pattern was this really testing?</label>
                <div class="flex flex-wrap gap-2">
                  <template x-for="p in currentQuestion.patterns.concat(currentQuestion.patternQuiz?.distractors || []).sort()" :key="p">
                    <button @click="stepData.selectedPattern=p" class="px-3 py-1.5 rounded-full text-sm border transition-all" :class="stepData.selectedPattern===p?'bg-indigo-100 border-indigo-400 text-indigo-700':'border-gray-200 text-gray-600 hover:border-gray-300'" x-text="p.replace(/-/g,' ')"></button>
                  </template>
                </div>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">What did you miss? (select all that apply)</label>
                <div class="flex flex-wrap gap-2">
                  <template x-for="m in mistakeCategories" :key="m">
                    <button @click="toggleMistake(m)" class="px-3 py-1.5 rounded-full text-sm border transition-all"
                      :class="stepData.mistakes.includes(m)?'bg-amber-100 border-amber-400 text-amber-700':'border-gray-200 text-gray-600 hover:border-gray-300'" x-text="m"></button>
                  </template>
                </div>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">Confidence for next time?</label>
                <input type="range" x-model.number="stepData.confidence" min="1" max="5" class="w-full accent-indigo-600">
                <div class="flex justify-between text-xs text-gray-400"><span>Not confident</span><span>Very confident</span></div>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Anything else to remember? (optional)</label>
                <textarea x-model="stepData.freeReflection" rows="2" placeholder="e.g. I keep forgetting to handle the empty array case" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- Hints panel --}}
        <div class="mt-4" x-show="hintsRevealed > 0" x-transition>
          <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-2">
            <div class="text-xs font-semibold text-blue-800 mb-1">Hints revealed</div>
            <template x-for="(h, i) in currentQuestion.hints.slice(0, hintsRevealed)" :key="i">
              <div class="text-sm text-blue-700"><span class="font-medium" x-text="'Level ' + (i+1) + ': '"></span><span x-text="h"></span></div>
            </template>
          </div>
        </div>

        {{-- Nav buttons --}}
        <div class="flex items-center justify-between mt-4">
          <div class="flex gap-2">
            <button @click="prevStep()" x-show="currentStep > 0" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Back</button>
            <button @click="revealHint()" x-show="currentStep < 10 && hintsRevealed < 5" class="px-4 py-2 border border-blue-200 rounded-lg text-sm text-blue-600 hover:bg-blue-50">
              Hint <span class="text-xs" x-text="'(' + hintsRevealed + '/5)'"></span>
            </button>
          </div>
          <div class="flex gap-2">
            <button @click="skipStep()" x-show="currentStep < 10" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-500 hover:bg-gray-50">Skip</button>
            <button @click="nextStep()" x-show="currentStep < 10" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Next step</button>
            <button @click="finishPractice()" x-show="currentStep === 10" class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">Finish & save</button>
          </div>
        </div>

        {{-- Save & exit --}}
        <div class="mt-3 text-center">
          <button @click="saveAndExit()" class="text-xs text-gray-400 hover:text-gray-600">Save and exit — pick up later</button>
        </div>
      </div>
    </div>
  </template>
</div>

{{-- ===== SOLUTION REVIEW VIEW ===== --}}
<div x-show="view==='review'" x-transition class="space-y-6">
  <template x-if="reviewQuestion">
    <div>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900" x-text="reviewQuestion.title + ' — Solution Review'"></h2>
        <button @click="go('landing')" class="text-sm text-gray-500 hover:text-gray-700">Back to home</button>
      </div>
      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4 text-sm text-emerald-800" x-text="encourage()"></div>
      <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
          <h3 class="font-semibold text-gray-900">Brute Force</h3>
          <p class="text-sm text-gray-700" x-text="reviewQuestion.bruteForce.approach"></p>
          <div class="text-xs font-mono bg-gray-50 rounded-lg p-3 whitespace-pre-wrap" x-text="reviewQuestion.bruteForce.code"></div>
          <div class="text-xs text-gray-500">Time: <span class="font-medium" x-text="reviewQuestion.bruteForce.timeComplexity"></span> &middot; Space: <span class="font-medium" x-text="reviewQuestion.bruteForce.spaceComplexity"></span></div>
        </div>
        <div class="bg-white border border-indigo-200 rounded-xl p-5 space-y-3">
          <h3 class="font-semibold text-indigo-900">Optimal</h3>
          <p class="text-sm text-gray-700" x-text="reviewQuestion.optimal.approach"></p>
          <div class="text-xs font-mono bg-indigo-50 rounded-lg p-3 whitespace-pre-wrap" x-text="reviewQuestion.optimal.code"></div>
          <div class="text-xs text-gray-500">Time: <span class="font-medium" x-text="reviewQuestion.optimal.timeComplexity"></span> &middot; Space: <span class="font-medium" x-text="reviewQuestion.optimal.spaceComplexity"></span></div>
        </div>
      </div>
      <div class="mt-4 bg-white border border-gray-200 rounded-xl p-5 space-y-3">
        <h3 class="font-semibold text-gray-900">Why the optimal approach works</h3>
        <p class="text-sm text-gray-700" x-text="reviewQuestion.solutionExplanation"></p>
      </div>
      <div class="mt-4 bg-white border border-gray-200 rounded-xl p-5 space-y-3">
        <h3 class="font-semibold text-gray-900">Edge cases</h3>
        <ul class="text-sm text-gray-700 list-disc ml-5 space-y-1"><template x-for="e in reviewQuestion.edgeCases" :key="e"><li x-text="e"></li></template></ul>
      </div>
      <div class="mt-4 bg-white border border-gray-200 rounded-xl p-5 space-y-3">
        <h3 class="font-semibold text-gray-900">Common interviewer follow-ups</h3>
        <ul class="text-sm text-gray-700 list-disc ml-5 space-y-1"><template x-for="f in reviewQuestion.followUps" :key="f"><li x-text="f"></li></template></ul>
      </div>
      <div class="mt-4 bg-white border border-gray-200 rounded-xl p-5 space-y-3">
        <h3 class="font-semibold text-gray-900">Common mistakes</h3>
        <ul class="text-sm text-gray-700 list-disc ml-5 space-y-1"><template x-for="m in reviewQuestion.commonMistakes" :key="m"><li x-text="m"></li></template></ul>
      </div>
      <div class="mt-6 flex gap-3">
        <button @click="startPractice(reviewQuestion)" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Practice this again</button>
        <button @click="go('bank')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Browse questions</button>
      </div>
    </div>
  </template>
</div>

{{-- ===== MOCK INTERVIEW VIEW ===== --}}
<div x-show="view==='mock'" x-transition class="space-y-4">
  <template x-if="mockState.active">
    <div>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">Mock Interview</h2>
        <button @click="endMock()" class="text-sm text-red-500 hover:text-red-700">End interview</button>
      </div>
      {{-- Timer --}}
      <div class="mb-4">
        <div class="flex items-center justify-between text-sm mb-1">
          <span class="text-gray-600" x-text="'Time: ' + formatTime(mockState.elapsed)"></span>
          <span class="text-gray-400" x-text="formatTime(mockState.total)"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div class="h-3 rounded-full transition-all duration-1000"
            :class="mockTimerPct() > 90 ? 'bg-red-500' : mockTimerPct() > 70 ? 'bg-amber-500' : 'bg-indigo-500'"
            :style="'width:' + Math.min(100, mockTimerPct()) + '%'"></div>
        </div>
      </div>
      {{-- Problem --}}
      <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <h3 class="font-semibold text-gray-900 mb-2" x-text="mockState.question.title"></h3>
        <p class="text-sm text-gray-700 whitespace-pre-line" x-text="mockState.question.statement"></p>
        <div class="mt-3 space-y-2">
          <template x-for="ex in mockState.question.examples" :key="ex.input">
            <div class="bg-gray-50 rounded-lg p-3 text-xs font-mono">
              <div><span class="text-gray-500">Input:</span> <span x-text="ex.input"></span></div>
              <div><span class="text-gray-500">Output:</span> <span x-text="ex.output"></span></div>
            </div>
          </template>
        </div>
      </div>
      {{-- Work area --}}
      <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <textarea x-model="mockState.answer" rows="12" placeholder="Work through the problem here — explain your approach, then code it..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono"></textarea>
      </div>
      {{-- Hints (hidden by default) --}}
      <div class="flex gap-2 mb-4">
        <button @click="mockRevealHint()" class="px-3 py-1.5 border border-blue-200 rounded-lg text-sm text-blue-600 hover:bg-blue-50" x-show="mockState.hintsUsed < 5">
          Request hint <span class="text-xs" x-text="'(' + mockState.hintsUsed + '/5)'"></span>
        </button>
      </div>
      <div x-show="mockState.hintsUsed > 0" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
        <template x-for="(h, i) in mockState.question.hints.slice(0, mockState.hintsUsed)" :key="i">
          <div class="text-sm text-blue-700 mb-1"><span class="font-medium" x-text="'Hint ' + (i+1) + ': '"></span><span x-text="h"></span></div>
        </template>
      </div>
      {{-- Follow-up questions revealed after 60% time --}}
      <div x-show="mockTimerPct() > 60" class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="text-xs font-semibold text-amber-800 mb-2">Interviewer follow-ups</div>
        <template x-for="(f, i) in mockState.question.followUps.slice(0, Math.floor((mockTimerPct()-60)/10)+1)" :key="i">
          <p class="text-sm text-amber-700 mb-1" x-text="f"></p>
        </template>
      </div>
    </div>
  </template>
  {{-- Mock setup --}}
  <template x-if="!mockState.active && view==='mock'">
    <div class="max-w-lg mx-auto text-center space-y-6 py-8">
      <h2 class="text-xl font-bold text-gray-900">Mock Interview Setup</h2>
      <div class="space-y-3">
        <label class="text-sm text-gray-600 block">Duration</label>
        <div class="flex justify-center gap-3">
          <button @click="mockState.duration=10" class="px-4 py-2 rounded-lg text-sm border-2 transition-all" :class="mockState.duration===10?'border-indigo-500 bg-indigo-50 text-indigo-700':'border-gray-200'">10 min (speed)</button>
          <button @click="mockState.duration=20" class="px-4 py-2 rounded-lg text-sm border-2 transition-all" :class="mockState.duration===20?'border-indigo-500 bg-indigo-50 text-indigo-700':'border-gray-200'">20 min</button>
          <button @click="mockState.duration=45" class="px-4 py-2 rounded-lg text-sm border-2 transition-all" :class="mockState.duration===45?'border-indigo-500 bg-indigo-50 text-indigo-700':'border-gray-200'">45 min</button>
        </div>
      </div>
      <button @click="startMock()" class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700">Start Mock Interview</button>
    </div>
  </template>
</div>

{{-- ===== WARM-UP VIEW ===== --}}
<div x-show="view==='warmup'" x-transition class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-900">Warm-Up Round</h2>
    <span class="text-sm text-gray-400" x-text="'Question ' + (warmup.index+1) + ' of ' + warmup.items.length"></span>
  </div>
  <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
    <div class="h-2 rounded-full bg-indigo-500 transition-all" :style="'width:' + ((warmup.index / warmup.items.length)*100) + '%'"></div>
  </div>
  <template x-if="warmup.items.length > 0 && warmup.index < warmup.items.length">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <template x-if="warmup.items[warmup.index].type === 'flashcard'">
        <div>
          <div class="text-xs font-medium text-indigo-600 mb-2">Flashcard</div>
          <p class="text-gray-900 font-medium mb-4" x-text="warmup.items[warmup.index].front"></p>
          <div x-show="warmup.revealed" x-transition class="bg-indigo-50 rounded-lg p-4 text-sm text-indigo-800" x-text="warmup.items[warmup.index].back"></div>
          <button @click="warmup.revealed=true" x-show="!warmup.revealed" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm mt-3">Reveal</button>
          <button @click="warmupNext()" x-show="warmup.revealed" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm mt-3">Next</button>
        </div>
      </template>
      <template x-if="warmup.items[warmup.index].type === 'pattern'">
        <div>
          <div class="text-xs font-medium text-emerald-600 mb-2">Pattern Recognition</div>
          <p class="text-gray-900 font-medium mb-4" x-text="warmup.items[warmup.index].summary"></p>
          <div class="flex flex-wrap gap-2">
            <template x-for="opt in warmup.items[warmup.index].options" :key="opt">
              <button @click="warmupAnswer(opt)" class="px-3 py-1.5 rounded-full text-sm border transition-all"
                :class="warmup.answered ? (opt === warmup.items[warmup.index].correct ? 'bg-green-100 border-green-400 text-green-700' : warmup.selected === opt ? 'bg-red-100 border-red-400 text-red-700' : 'border-gray-200 text-gray-400') : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                x-text="opt.replace(/-/g,' ')" :disabled="warmup.answered"></button>
            </template>
          </div>
          <button @click="warmupNext()" x-show="warmup.answered" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm mt-3">Next</button>
        </div>
      </template>
      <template x-if="warmup.items[warmup.index].type === 'complexity'">
        <div>
          <div class="text-xs font-medium text-amber-600 mb-2">Complexity Check</div>
          <pre class="bg-gray-50 rounded-lg p-4 text-sm font-mono mb-4 whitespace-pre-wrap" x-text="warmup.items[warmup.index].code"></pre>
          <div class="flex flex-wrap gap-2">
            <template x-for="opt in warmup.items[warmup.index].options" :key="opt">
              <button @click="warmupAnswer(opt)" class="px-3 py-1.5 rounded-full text-sm border transition-all"
                :class="warmup.answered ? (opt === warmup.items[warmup.index].correct ? 'bg-green-100 border-green-400 text-green-700' : warmup.selected === opt ? 'bg-red-100 border-red-400 text-red-700' : 'border-gray-200 text-gray-400') : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                x-text="opt" :disabled="warmup.answered"></button>
            </template>
          </div>
          <div x-show="warmup.answered" class="text-sm text-gray-600 mt-3" x-text="warmup.items[warmup.index].explanation"></div>
          <button @click="warmupNext()" x-show="warmup.answered" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm mt-3">Next</button>
        </div>
      </template>
    </div>
  </template>
  <template x-if="warmup.index >= warmup.items.length && warmup.items.length > 0">
    <div class="text-center py-8">
      <div class="text-3xl mb-2">&#10003;</div>
      <div class="text-lg font-bold text-gray-900 mb-1">Warm-up complete!</div>
      <div class="text-sm text-gray-500 mb-4" x-text="warmup.correct + '/' + warmup.items.length + ' correct — momentum built!'"></div>
      <button @click="go('landing')" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Continue to practice</button>
    </div>
  </template>
</div>

{{-- ===== PATTERN DRILL VIEW ===== --}}
<div x-show="view==='patternDrill'" x-transition class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-900">Pattern Recognition Drill</h2>
    <span class="text-sm text-gray-400" x-text="'Score: ' + drill.correct + '/' + drill.total"></span>
  </div>
  <template x-if="drill.currentQ">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <p class="text-sm text-gray-500 mb-2">What pattern does this problem suggest?</p>
      <p class="text-gray-900 font-medium mb-4" x-text="drill.currentQ.summary"></p>
      <div class="flex flex-wrap gap-2 mb-4">
        <template x-for="opt in drill.currentQ.options" :key="opt">
          <button @click="drillAnswer(opt)" class="px-4 py-2 rounded-lg text-sm border-2 transition-all"
            :class="drill.answered ? (opt===drill.currentQ.correct ? 'bg-green-100 border-green-400 text-green-700' : drill.selected===opt ? 'bg-red-100 border-red-400 text-red-700' : 'border-gray-200 text-gray-400') : 'border-gray-200 text-gray-700 hover:border-indigo-300'"
            x-text="opt.replace(/-/g,' ')" :disabled="drill.answered"></button>
        </template>
      </div>
      <div x-show="drill.answered" x-transition class="text-sm text-gray-600 mb-3" x-text="drill.currentQ.explanation"></div>
      <button @click="nextDrillQ()" x-show="drill.answered" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Next question</button>
    </div>
  </template>
  <template x-if="!drill.currentQ && drill.total > 0">
    <div class="text-center py-8">
      <div class="text-lg font-bold text-gray-900 mb-1">Drill complete!</div>
      <div class="text-sm text-gray-500 mb-4" x-text="drill.correct + '/' + drill.total + ' correct'"></div>
      <div class="flex justify-center gap-3">
        <button @click="startPatternDrill()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Again</button>
        <button @click="go('landing')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Home</button>
      </div>
    </div>
  </template>
</div>

{{-- ===== COMPLEXITY DRILL VIEW ===== --}}
<div x-show="view==='complexityDrill'" x-transition class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-900">Complexity Drill</h2>
    <span class="text-sm text-gray-400" x-text="'Score: ' + compDrill.correct + '/' + compDrill.total"></span>
  </div>
  <template x-if="compDrill.currentQ">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
      <p class="text-sm text-gray-500 mb-2" x-text="compDrill.currentQ.question"></p>
      <pre class="bg-gray-50 rounded-lg p-4 text-sm font-mono mb-4 whitespace-pre-wrap" x-text="compDrill.currentQ.code"></pre>
      <div class="flex flex-wrap gap-2 mb-4">
        <template x-for="opt in compDrill.currentQ.options" :key="opt">
          <button @click="compDrillAnswer(opt)" class="px-4 py-2 rounded-lg text-sm border-2 transition-all"
            :class="compDrill.answered ? (opt===compDrill.currentQ.correct ? 'bg-green-100 border-green-400 text-green-700' : compDrill.selected===opt ? 'bg-red-100 border-red-400 text-red-700' : 'border-gray-200 text-gray-400') : 'border-gray-200 text-gray-700 hover:border-indigo-300'"
            x-text="opt" :disabled="compDrill.answered"></button>
        </template>
      </div>
      <div x-show="compDrill.answered" x-transition class="space-y-2 mb-3">
        <p class="text-sm text-gray-600" x-text="compDrill.currentQ.explanation"></p>
        <template x-if="compDrill.currentQ.canImprove">
          <p class="text-sm text-indigo-600 font-medium" x-text="'Can be improved: ' + compDrill.currentQ.improvement"></p>
        </template>
      </div>
      <button @click="nextCompDrillQ()" x-show="compDrill.answered" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Next</button>
    </div>
  </template>
  <template x-if="!compDrill.currentQ && compDrill.total > 0">
    <div class="text-center py-8">
      <div class="text-lg font-bold text-gray-900 mb-1">Drill complete!</div>
      <div class="text-sm text-gray-500 mb-4" x-text="compDrill.correct + '/' + compDrill.total + ' correct'"></div>
      <div class="flex justify-center gap-3">
        <button @click="startComplexityDrill()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Again</button>
        <button @click="go('landing')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Home</button>
      </div>
    </div>
  </template>
</div>

{{-- ===== FLASHCARDS VIEW ===== --}}
<div x-show="view==='flashcards'" x-transition class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-900">Rapid Revision</h2>
    <span class="text-sm text-gray-400" x-text="fc.index + '/' + fc.deck.length"></span>
  </div>
  <div class="w-full bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full bg-indigo-500 transition-all" :style="'width:' + (fc.deck.length ? (fc.index/fc.deck.length)*100 : 0) + '%'"></div></div>
  <template x-if="fc.deck.length > 0 && fc.index < fc.deck.length">
    <div @click="fc.flipped=!fc.flipped" class="bg-white border-2 border-gray-200 rounded-2xl p-8 min-h-[200px] flex items-center justify-center cursor-pointer hover:border-indigo-300 transition-all">
      <div class="text-center">
        <div class="text-xs text-gray-400 mb-3" x-text="fc.flipped ? 'Answer' : 'Question'"></div>
        <p class="text-lg font-medium text-gray-900" x-text="fc.flipped ? fc.deck[fc.index].back : fc.deck[fc.index].front"></p>
      </div>
    </div>
  </template>
  <div class="flex justify-center gap-3" x-show="fc.flipped">
    <button @click="fcRate(1)" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">Didn't know</button>
    <button @click="fcRate(3)" class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg text-sm hover:bg-amber-200">Kinda knew</button>
    <button @click="fcRate(5)" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">Knew it</button>
  </div>
  <template x-if="fc.index >= fc.deck.length && fc.deck.length > 0">
    <div class="text-center py-8">
      <div class="text-lg font-bold text-gray-900 mb-1">Deck complete!</div>
      <div class="text-sm text-gray-500 mb-4" x-text="fc.known + ' knew / ' + fc.deck.length + ' total'"></div>
      <div class="flex justify-center gap-3">
        <button @click="startFlashcards()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Reshuffle</button>
        <button @click="go('landing')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Home</button>
      </div>
    </div>
  </template>
</div>

{{-- ===== FOCUS MAP VIEW ===== --}}
<div x-show="view==='focusMap'" x-transition class="space-y-6">
  <h2 class="text-lg font-bold text-gray-900">Where to Level Up</h2>

  {{-- Improvements this week --}}
  <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
    <div class="text-sm font-semibold text-emerald-800 mb-2">Things you've improved this week</div>
    <template x-if="weeklyImprovements().length > 0">
      <ul class="text-sm text-emerald-700 space-y-1"><template x-for="imp in weeklyImprovements().slice(0,3)" :key="imp"><li x-text="imp"></li></template></ul>
    </template>
    <template x-if="weeklyImprovements().length === 0">
      <p class="text-sm text-emerald-600">Start practicing to see your progress here!</p>
    </template>
  </div>

  {{-- Key metrics --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold tabular-nums" x-text="solveRate() + '%'"></div>
      <div class="text-xs text-gray-500 mt-1">Solve rate</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold tabular-nums" x-text="solveRateNoHints() + '%'"></div>
      <div class="text-xs text-gray-500 mt-1">Without hints</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold tabular-nums" x-text="avgTime() + 'm'"></div>
      <div class="text-xs text-gray-500 mt-1">Avg time</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold tabular-nums" x-text="complexityAccuracy() + '%'"></div>
      <div class="text-xs text-gray-500 mt-1">Complexity accuracy</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold tabular-nums" x-text="patternAccuracy() + '%'"></div>
      <div class="text-xs text-gray-500 mt-1">Pattern accuracy</div>
    </div>
  </div>

  {{-- Weak / strong areas --}}
  <div class="grid gap-4 lg:grid-cols-2">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
      <h3 class="text-sm font-semibold text-gray-700 mb-3">Growth areas</h3>
      <template x-if="getWeakPatterns().length > 0">
        <div class="space-y-2"><template x-for="w in getWeakPatterns().slice(0,5)" :key="w.pattern">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-700" x-text="w.pattern.replace(/-/g,' ')"></span>
            <span class="text-xs text-amber-600 font-medium" x-text="w.rate + '% solved'"></span>
          </div>
        </template></div>
      </template>
      <template x-if="getWeakPatterns().length === 0"><p class="text-sm text-gray-400">Not enough data yet</p></template>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
      <h3 class="text-sm font-semibold text-gray-700 mb-3">Strengths</h3>
      <template x-if="getStrongPatterns().length > 0">
        <div class="space-y-2"><template x-for="s in getStrongPatterns().slice(0,5)" :key="s.pattern">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-700" x-text="s.pattern.replace(/-/g,' ')"></span>
            <span class="text-xs text-emerald-600 font-medium" x-text="s.rate + '% solved'"></span>
          </div>
        </template></div>
      </template>
      <template x-if="getStrongPatterns().length === 0"><p class="text-sm text-gray-400">Not enough data yet</p></template>
    </div>
  </div>

  {{-- CTA --}}
  <template x-if="getWeakPatterns().length > 0">
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-center">
      <p class="text-sm text-indigo-800 mb-3" x-text="'Your biggest lever right now is ' + getWeakPatterns()[0].pattern.replace(/-/g,' ') + '. Start a 10-minute drill?'"></p>
      <button @click="startPatternDrillFor(getWeakPatterns()[0].pattern)" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Let's go</button>
    </div>
  </template>
</div>

{{-- ===== SETTINGS VIEW ===== --}}
<div x-show="view==='settings'" x-transition class="max-w-lg mx-auto space-y-6">
  <h2 class="text-lg font-bold text-gray-900">Settings</h2>
  <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
    <div>
      <label class="text-sm font-medium text-gray-700 block mb-1">Stuck timer nudge (minutes)</label>
      <input type="number" x-model.number="user.prefs.nudgeInterval" min="1" max="30" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-24">
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700 block mb-1">Default template mode</label>
      <select x-model="user.prefs.templateMode" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <option value="template">Sentence starters</option>
        <option value="freeform">Freeform</option>
      </select>
    </div>
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-700">Warm-up before sessions</label>
      <input type="checkbox" x-model="user.prefs.warmupEnabled" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
    </div>
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-700">Pomodoro timer (25 min blocks)</label>
      <input type="checkbox" x-model="user.prefs.pomodoroEnabled" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
    </div>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
    <h3 class="text-sm font-semibold text-gray-700">Data</h3>
    <div class="flex gap-3">
      <button @click="exportData()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Export progress</button>
      <button @click="if(confirm('Reset all progress? This cannot be undone.'))resetData()" class="px-4 py-2 border border-red-200 rounded-lg text-sm text-red-600 hover:bg-red-50">Reset all data</button>
    </div>
  </div>
</div>

{{-- Toast --}}
<div x-show="toast" x-transition.opacity class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2 rounded-xl text-sm shadow-lg" x-text="toast"></div>

</div>

{{-- ========================== SCRIPTS ========================== --}}
@push('scripts')
<script>
function techInterviewPrep(){return{
view:'landing',currentQuestion:null,reviewQuestion:null,currentStep:0,hintsRevealed:0,
showProblem:true,showStuckNudge:false,stuckTimerId:null,toast:null,

stepLabels:['Restate','Clarify','I/O/Constraints','Brute Force','Trade-offs','Optimal','Time O(?)','Space O(?)','Edge Cases','Code','Reflect'],
mistakeCategories:['Missed pattern','Wrong time complexity','Wrong space complexity','Forgot edge cases','Brute force only','Coding bug','Logic bug','Incomplete explanation','Poor trade-off reasoning'],

stepData:{useTemplates:true,restate:'',inputOutput:'',assumptions:'',inputs:'',outputs:'',constraints:'',bruteForce:'',bruteForceTC:'',tradeoffs:'',optimal:'',timeComplexity:'',spaceComplexity:'',edgeCases:'',solution:'',notes:'',selectedPattern:'',mistakes:[],confidence:3,freeReflection:'',skippedSteps:[]},

practiceTimer:{running:false,elapsed:0,total:0,interval:null},
mockState:{active:false,duration:20,question:null,elapsed:0,total:0,interval:null,answer:'',hintsUsed:0},
warmup:{items:[],index:0,revealed:false,answered:false,selected:null,correct:0},
drill:{pool:[],currentQ:null,answered:false,selected:null,correct:0,total:0},
compDrill:{pool:[],currentQ:null,answered:false,selected:null,correct:0,total:0},
fc:{deck:[],index:0,flipped:false,known:0},
filters:{category:'',difficulty:'',pattern:'',cognitiveLoad:''},

user:{
  attempts:[],reflections:[],
  streak:{current:0,longest:0,lastDate:null,freezeUsed:false},
  bookmark:null,
  prefs:{nudgeInterval:3,templateMode:'template',warmupEnabled:true,pomodoroEnabled:true},
  drillStats:{patternCorrect:0,patternTotal:0,complexityCorrect:0,complexityTotal:0},
  sessionLogs:[],
  fcStats:{}
},

get allCategories(){return[...new Set(this.questions.map(q=>q.category))].sort()},
get allPatterns(){return[...new Set(this.questions.flatMap(q=>q.patterns))].sort()},

init(){
  this.loadData();
  this.updateStreak();
  this.stepData.useTemplates=this.user.prefs.templateMode==='template';
},

loadData(){try{const d=localStorage.getItem('tip_user');if(d)this.user={...this.user,...JSON.parse(d)}}catch(e){}},
saveData(){try{localStorage.setItem('tip_user',JSON.stringify(this.user))}catch(e){}},

go(v){
  this.view=v;
  this.clearTimers();
  if(v==='patternDrill')this.startPatternDrill();
  if(v==='complexityDrill')this.startComplexityDrill();
  if(v==='flashcards')this.startFlashcards();
},

showToast(msg){this.toast=msg;setTimeout(()=>this.toast=null,2500)},
encourage(){const m=['You\'ve now seen this pattern — next time you\'ll recognise it faster.','Every attempt builds pattern recognition. Keep going.','Mistakes are data, not failure. You\'re building intuition.','You just expanded your problem-solving toolkit.','This is how experts are made — one problem at a time.','The fact that you showed up today matters. Consistency beats intensity.','You\'re training your brain to see structure where others see chaos.'];return m[Math.floor(Math.random()*m.length)]},

formatTime(s){const m=Math.floor(s/60),sec=s%60;return m+':'+(sec<10?'0':'')+sec},
timerPct(){return this.practiceTimer.total?Math.round(this.practiceTimer.elapsed/this.practiceTimer.total*100):0},
mockTimerPct(){return this.mockState.total?Math.round(this.mockState.elapsed/this.mockState.total*100):0},

clearTimers(){
  if(this.practiceTimer.interval){clearInterval(this.practiceTimer.interval);this.practiceTimer.interval=null;this.practiceTimer.running=false}
  if(this.mockState.interval){clearInterval(this.mockState.interval);this.mockState.interval=null}
  if(this.stuckTimerId){clearTimeout(this.stuckTimerId);this.stuckTimerId=null}
  this.showStuckNudge=false;
},

startStuckTimer(){
  if(this.stuckTimerId)clearTimeout(this.stuckTimerId);
  this.showStuckNudge=false;
  this.stuckTimerId=setTimeout(()=>{this.showStuckNudge=true},this.user.prefs.nudgeInterval*60*1000);
},

dismissStuckNudge(reason){this.showStuckNudge=false;if(reason==='thinking')this.startStuckTimer()},

// ===== PRACTICE =====
startPractice(q){
  this.currentQuestion=q;this.reviewQuestion=null;this.currentStep=0;this.hintsRevealed=0;
  this.stepData={useTemplates:this.user.prefs.templateMode==='template',restate:'',inputOutput:'',assumptions:'',inputs:'',outputs:'',constraints:'',bruteForce:'',bruteForceTC:'',tradeoffs:'',optimal:'',timeComplexity:'',spaceComplexity:'',edgeCases:'',solution:'',notes:'',selectedPattern:'',mistakes:[],confidence:3,freeReflection:'',skippedSteps:[]};
  this.practiceTimer={running:true,elapsed:0,total:q.recommendedTime*60,interval:null};
  this.practiceTimer.interval=setInterval(()=>{this.practiceTimer.elapsed++},1000);
  this.view='practice';
  this.startStuckTimer();
  this.logSession('practice',q.id);
},

nextStep(){if(this.currentStep<10){this.currentStep++;this.startStuckTimer();this.saveBookmark()}},
prevStep(){if(this.currentStep>0){this.currentStep--;this.startStuckTimer()}},
skipStep(){this.stepData.skippedSteps.push(this.currentStep);this.nextStep()},

revealHint(){if(this.currentQuestion&&this.hintsRevealed<5)this.hintsRevealed++},

toggleMistake(m){const i=this.stepData.mistakes.indexOf(m);i>=0?this.stepData.mistakes.splice(i,1):this.stepData.mistakes.push(m)},

saveBookmark(){
  if(!this.currentQuestion)return;
  this.user.bookmark={questionId:this.currentQuestion.id,step:this.currentStep,stepData:{...this.stepData},hintsRevealed:this.hintsRevealed,elapsed:this.practiceTimer.elapsed};
  this.saveData();
},

resumeBookmark(){
  if(!this.user.bookmark)return;
  const q=this.getQ(this.user.bookmark.questionId);if(!q)return;
  this.currentQuestion=q;this.currentStep=this.user.bookmark.step;
  this.stepData={...this.stepData,...this.user.bookmark.stepData};
  this.hintsRevealed=this.user.bookmark.hintsRevealed||0;
  this.practiceTimer={running:true,elapsed:this.user.bookmark.elapsed||0,total:q.recommendedTime*60,interval:null};
  this.practiceTimer.interval=setInterval(()=>{this.practiceTimer.elapsed++},1000);
  this.view='practice';this.startStuckTimer();
},

saveAndExit(){this.saveBookmark();this.clearTimers();this.view='landing';this.showToast('Progress saved — pick up anytime')},

finishPractice(){
  this.clearTimers();
  const solved=this.stepData.confidence>=3&&this.stepData.solution.trim().length>0;
  const attempt={questionId:this.currentQuestion.id,date:new Date().toISOString(),solved,hintsUsed:this.hintsRevealed,timeTaken:this.practiceTimer.elapsed,confidence:this.stepData.confidence,selectedPattern:this.stepData.selectedPattern,actualPatterns:this.currentQuestion.patterns,skippedSteps:this.stepData.skippedSteps,mistakes:this.stepData.mistakes};
  this.user.attempts.push(attempt);
  if(this.stepData.selectedPattern||this.stepData.mistakes.length||this.stepData.freeReflection){
    this.user.reflections.push({questionId:this.currentQuestion.id,date:new Date().toISOString(),pattern:this.stepData.selectedPattern,mistakes:this.stepData.mistakes,confidence:this.stepData.confidence,note:this.stepData.freeReflection});
  }
  this.scheduleReview(this.currentQuestion.id,this.stepData.confidence);
  this.user.bookmark=null;this.updateStreak();this.saveData();
  this.reviewQuestion=this.currentQuestion;this.view='review';
  this.showToast(this.encourage());
},

// ===== MOCK INTERVIEW =====
startMock(){
  const pool=this.questions.filter(q=>q.difficulty!=='hard');
  const q=pool[Math.floor(Math.random()*pool.length)];
  this.mockState={active:true,duration:this.mockState.duration,question:q,elapsed:0,total:this.mockState.duration*60,interval:null,answer:'',hintsUsed:0};
  this.mockState.interval=setInterval(()=>{this.mockState.elapsed++;if(this.mockState.elapsed>=this.mockState.total)this.endMock()},1000);
  this.logSession('mock',q.id);
},

mockRevealHint(){if(this.mockState.hintsUsed<5)this.mockState.hintsUsed++},

endMock(){
  if(this.mockState.interval)clearInterval(this.mockState.interval);
  this.mockState.active=false;
  const attempt={questionId:this.mockState.question.id,date:new Date().toISOString(),solved:this.mockState.answer.trim().length>50,hintsUsed:this.mockState.hintsUsed,timeTaken:this.mockState.elapsed,confidence:3,actualPatterns:this.mockState.question.patterns,skippedSteps:[],mistakes:[]};
  this.user.attempts.push(attempt);this.updateStreak();this.saveData();
  this.reviewQuestion=this.mockState.question;this.view='review';
},

// ===== WARM-UP =====
startWarmUp(){
  const items=[];
  const fcs=this.shuffle([...this.flashcards]).slice(0,1);
  fcs.forEach(f=>items.push({type:'flashcard',front:f.front,back:f.back}));
  const pqs=this.shuffle(this.questions.filter(q=>q.patternQuiz)).slice(0,1);
  pqs.forEach(q=>items.push({type:'pattern',summary:q.patternQuiz.summary,correct:q.patternQuiz.correct,options:this.shuffle([q.patternQuiz.correct,...q.patternQuiz.distractors]),explanation:'The pattern here is '+q.patternQuiz.correct.replace(/-/g,' ')+'.'}));
  const cds=this.shuffle([...this.complexityDrills]).slice(0,1);
  cds.forEach(d=>items.push({type:'complexity',code:d.code,correct:d.correct,options:d.options,explanation:d.explanation}));
  this.warmup={items:this.shuffle(items),index:0,revealed:false,answered:false,selected:null,correct:0};
  this.view='warmup';this.logSession('warmup',null);
},

warmupAnswer(opt){
  this.warmup.answered=true;this.warmup.selected=opt;
  const item=this.warmup.items[this.warmup.index];
  if(opt===item.correct)this.warmup.correct++;
},

warmupNext(){this.warmup.index++;this.warmup.revealed=false;this.warmup.answered=false;this.warmup.selected=null;
  if(this.warmup.index>=this.warmup.items.length){this.updateStreak();this.saveData()}
},

// ===== PATTERN DRILL =====
startPatternDrill(){
  const pool=this.shuffle(this.questions.filter(q=>q.patternQuiz)).slice(0,10).map(q=>({summary:q.patternQuiz.summary,correct:q.patternQuiz.correct,options:this.shuffle([q.patternQuiz.correct,...q.patternQuiz.distractors]),explanation:'This problem uses '+q.patternQuiz.correct.replace(/-/g,' ')+'. '+q.statement.substring(0,80)+'...'}));
  this.drill={pool,currentQ:pool[0]||null,answered:false,selected:null,correct:0,total:0};
},

startPatternDrillFor(pattern){
  const pool=this.shuffle(this.questions.filter(q=>q.patternQuiz&&q.patterns.includes(pattern))).slice(0,10).map(q=>({summary:q.patternQuiz.summary,correct:q.patternQuiz.correct,options:this.shuffle([q.patternQuiz.correct,...q.patternQuiz.distractors]),explanation:'This problem uses '+q.patternQuiz.correct.replace(/-/g,' ')+'.'}));
  this.drill={pool,currentQ:pool[0]||null,answered:false,selected:null,correct:0,total:0};
  this.view='patternDrill';
},

drillAnswer(opt){
  this.drill.answered=true;this.drill.selected=opt;this.drill.total++;
  if(opt===this.drill.currentQ.correct){this.drill.correct++;this.user.drillStats.patternCorrect++}
  this.user.drillStats.patternTotal++;this.saveData();
},

nextDrillQ(){
  const i=this.drill.pool.indexOf(this.drill.currentQ);
  this.drill.currentQ=this.drill.pool[i+1]||null;this.drill.answered=false;this.drill.selected=null;
},

// ===== COMPLEXITY DRILL =====
startComplexityDrill(){
  const pool=this.shuffle([...this.complexityDrills]).slice(0,10);
  this.compDrill={pool,currentQ:pool[0]||null,answered:false,selected:null,correct:0,total:0};
},

compDrillAnswer(opt){
  this.compDrill.answered=true;this.compDrill.selected=opt;this.compDrill.total++;
  if(opt===this.compDrill.currentQ.correct){this.compDrill.correct++;this.user.drillStats.complexityCorrect++}
  this.user.drillStats.complexityTotal++;this.saveData();
},

nextCompDrillQ(){
  const i=this.compDrill.pool.indexOf(this.compDrill.currentQ);
  this.compDrill.currentQ=this.compDrill.pool[i+1]||null;this.compDrill.answered=false;this.compDrill.selected=null;
},

// ===== FLASHCARDS =====
startFlashcards(){
  this.fc={deck:this.shuffle([...this.flashcards]),index:0,flipped:false,known:0};
  this.logSession('flashcards',null);
},

fcRate(score){
  if(score>=4)this.fc.known++;
  this.fc.index++;this.fc.flipped=false;
  if(this.fc.index>=this.fc.deck.length){this.updateStreak();this.saveData()}
},

// ===== ENERGY SESSIONS =====
startEnergySession(min){
  if(min===10){
    const modes=['patternDrill','complexityDrill','flashcards'];
    this.go(modes[Math.floor(Math.random()*modes.length)]);
  } else if(min===30){
    if(this.user.prefs.warmupEnabled){this.startWarmUp();return}
    const q=this.pickQuestion();if(q)this.startPractice(q);
  } else {
    if(this.user.prefs.warmupEnabled){this.startWarmUp();return}
    this.view='mock';
  }
},

surpriseMe(){
  const review=this.getReviewQueue();
  const q=review.length?review[Math.floor(Math.random()*review.length)]:this.questions[Math.floor(Math.random()*this.questions.length)];
  this.startPractice(q);
},

pickQuestion(){
  const review=this.getReviewQueue();
  if(review.length)return review[0];
  const unattempted=this.questions.filter(q=>!this.user.attempts.find(a=>a.questionId===q.id));
  if(unattempted.length)return unattempted[Math.floor(Math.random()*unattempted.length)];
  return this.questions[Math.floor(Math.random()*this.questions.length)];
},

// ===== SM-2 SPACED REPETITION =====
scheduleReview(qId,quality){
  let sched=this.user.reviewSchedules||{};
  let s=sched[qId]||{interval:1,ef:2.5,reps:0};
  const q=Math.max(0,Math.min(5,quality));
  if(q<3){s.reps=0;s.interval=1}
  else{if(s.reps===0)s.interval=1;else if(s.reps===1)s.interval=6;else s.interval=Math.round(s.interval*s.ef);s.reps++}
  s.ef=Math.max(1.3,s.ef+0.1-(5-q)*(0.08+(5-q)*0.02));
  s.nextDate=new Date(Date.now()+s.interval*86400000).toISOString().slice(0,10);
  sched[qId]=s;this.user.reviewSchedules=sched;this.saveData();
},

getReviewQueue(){
  const today=new Date().toISOString().slice(0,10);
  const sched=this.user.reviewSchedules||{};
  return Object.entries(sched).filter(([_,s])=>s.nextDate<=today).map(([id])=>this.getQ(parseInt(id))).filter(Boolean).slice(0,5);
},

// ===== STREAK =====
updateStreak(){
  const today=new Date().toISOString().slice(0,10);
  if(this.user.streak.lastDate===today)return;
  const yesterday=new Date(Date.now()-86400000).toISOString().slice(0,10);
  if(this.user.streak.lastDate===yesterday){this.user.streak.current++}
  else if(this.user.streak.lastDate){this.user.streak.current=1}
  else{this.user.streak.current=1}
  this.user.streak.lastDate=today;
  if(this.user.streak.current>this.user.streak.longest)this.user.streak.longest=this.user.streak.current;
  this.saveData();
},

// ===== ANALYTICS =====
getQ(id){return this.questions.find(q=>q.id===id)},
getAttempts(qId){return this.user.attempts.filter(a=>a.questionId===qId)},
lastAttemptSolved(qId){const a=this.getAttempts(qId);return a.length?a[a.length-1].solved:false},
todaySessions(){const t=new Date().toISOString().slice(0,10);return this.user.sessionLogs.filter(s=>s.date&&s.date.startsWith(t)).length},
totalSolved(){return new Set(this.user.attempts.filter(a=>a.solved).map(a=>a.questionId)).size},
solveRate(){const a=this.user.attempts;if(!a.length)return 0;return Math.round(a.filter(x=>x.solved).length/a.length*100)},
solveRateNoHints(){const a=this.user.attempts.filter(x=>x.solved);if(!a.length)return 0;return Math.round(a.filter(x=>x.hintsUsed===0).length/a.length*100)},
avgTime(){const a=this.user.attempts.filter(x=>x.timeTaken);if(!a.length)return 0;return Math.round(a.reduce((s,x)=>s+x.timeTaken,0)/a.length/60)},
complexityAccuracy(){const s=this.user.drillStats;if(!s.complexityTotal)return 0;return Math.round(s.complexityCorrect/s.complexityTotal*100)},
patternAccuracy(){const s=this.user.drillStats;if(!s.patternTotal)return 0;return Math.round(s.patternCorrect/s.patternTotal*100)},

filteredQuestions(){
  return this.questions.filter(q=>{
    if(this.filters.category&&q.category!==this.filters.category)return false;
    if(this.filters.difficulty&&q.difficulty!==this.filters.difficulty)return false;
    if(this.filters.pattern&&!q.patterns.includes(this.filters.pattern))return false;
    if(this.filters.cognitiveLoad&&q.cognitiveLoad!==this.filters.cognitiveLoad)return false;
    return true;
  });
},

getWeakPatterns(){
  const map={};
  this.user.attempts.forEach(a=>{
    (a.actualPatterns||[]).forEach(p=>{
      if(!map[p])map[p]={solved:0,total:0};
      map[p].total++;if(a.solved)map[p].solved++;
    });
  });
  return Object.entries(map).map(([pattern,d])=>({pattern,rate:Math.round(d.solved/d.total*100)})).filter(x=>x.rate<70).sort((a,b)=>a.rate-b.rate);
},

getStrongPatterns(){
  const map={};
  this.user.attempts.forEach(a=>{
    (a.actualPatterns||[]).forEach(p=>{
      if(!map[p])map[p]={solved:0,total:0};
      map[p].total++;if(a.solved)map[p].solved++;
    });
  });
  return Object.entries(map).map(([pattern,d])=>({pattern,rate:Math.round(d.solved/d.total*100)})).filter(x=>x.rate>=70).sort((a,b)=>b.rate-a.rate);
},

weeklyImprovements(){
  const week=Date.now()-7*86400000;const recent=this.user.attempts.filter(a=>new Date(a.date)>week);
  const imps=[];
  if(recent.filter(a=>a.solved).length>0)imps.push('Solved '+recent.filter(a=>a.solved).length+' problem(s) this week');
  if(recent.filter(a=>a.hintsUsed===0&&a.solved).length>0)imps.push(recent.filter(a=>a.hintsUsed===0&&a.solved).length+' solved without hints');
  const pats=new Set(recent.flatMap(a=>a.actualPatterns||[]));
  if(pats.size>0)imps.push('Practiced '+pats.size+' different pattern(s)');
  return imps;
},

logSession(mode,qId){this.user.sessionLogs.push({date:new Date().toISOString(),mode,questionId:qId});this.saveData()},

// ===== DATA =====
exportData(){
  const blob=new Blob([JSON.stringify(this.user,null,2)],{type:'application/json'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='interview-prep-progress.json';a.click();
},

resetData(){
  this.user={attempts:[],reflections:[],streak:{current:0,longest:0,lastDate:null,freezeUsed:false},bookmark:null,prefs:this.user.prefs,drillStats:{patternCorrect:0,patternTotal:0,complexityCorrect:0,complexityTotal:0},sessionLogs:[],fcStats:{},reviewSchedules:{}};
  this.saveData();this.showToast('All progress reset');
},

// ===== KEYBOARD =====
handleKey(e){
  if(e.target.tagName==='TEXTAREA'||e.target.tagName==='INPUT')return;
  if(this.view==='practice'){
    if(e.key==='h'||e.key==='H')this.revealHint();
    if(e.key==='n'||e.key==='N')this.nextStep();
    if(e.key==='s'||e.key==='S')this.skipStep();
  }
},

shuffle(arr){for(let i=arr.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[arr[i],arr[j]]=[arr[j],arr[i]]}return arr},

// ===== QUESTION BANK =====
questions:[
{id:1,title:'Two Sum',difficulty:'easy',category:'arrays',patterns:['hash-map'],cognitiveLoad:'low',recommendedTime:15,
statement:'Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.\n\nYou may assume that each input would have exactly one solution, and you may not use the same element twice.',
examples:[{input:'nums = [2,7,11,15], target = 9',output:'[0,1]',explanation:'Because nums[0] + nums[1] == 9'}],
commonMistakes:['Using the same element twice','Not handling negative numbers','Returning values instead of indices'],
discussionPoints:['Hash map for O(1) lookups','Single-pass vs two-pass','What if sorted? (two pointers)'],
bruteForce:{approach:'Check every pair of numbers using two nested loops.',timeComplexity:'O(n²)',spaceComplexity:'O(1)',code:'for i in range(len(nums)):\n  for j in range(i+1, len(nums)):\n    if nums[i] + nums[j] == target:\n      return [i, j]'},
optimal:{approach:'Use a hash map to store each number\'s index as you iterate. For each number, check if (target - number) exists in the map.',timeComplexity:'O(n)',spaceComplexity:'O(n)',code:'seen = {}\nfor i, num in enumerate(nums):\n  comp = target - num\n  if comp in seen:\n    return [seen[comp], i]\n  seen[num] = i'},
hints:['Think about what value you need to find for each number.','A data structure with O(1) lookup would help.','Hash map: store values you\'ve seen and their indices.','For each num, check if (target - num) is in the map.','Single pass: check then insert into the map.'],
edgeCases:['Array with exactly 2 elements','Negative numbers','Two identical numbers that sum to target'],
followUps:['What if the array is sorted?','What if you need all pairs?','What if there are duplicates?','Can you do it in constant space?'],
solutionExplanation:'The key insight is that for each number, there\'s exactly one complement (target - number) that would make a valid pair. A hash map lets us check if we\'ve already seen this complement in O(1) time. One pass through the array is enough.',
patternQuiz:{summary:'Given an array of numbers, find two that sum to a target value.',correct:'hash-map',distractors:['two-pointers','binary-search','sliding-window']}},

{id:2,title:'Valid Parentheses',difficulty:'easy',category:'stacks',patterns:['stack'],cognitiveLoad:'low',recommendedTime:10,
statement:'Given a string s containing just the characters \'(\', \')\', \'{\', \'}\', \'[\' and \']\', determine if the input string is valid.\n\nAn input string is valid if:\n1. Open brackets must be closed by the same type of brackets.\n2. Open brackets must be closed in the correct order.\n3. Every close bracket has a corresponding open bracket of the same type.',
examples:[{input:'s = "()"',output:'true',explanation:''},{input:'s = "([)]"',output:'false',explanation:'Brackets are not closed in correct order'}],
commonMistakes:['Forgetting to check stack is empty at the end','Not handling the case where stack is empty but we get a closing bracket','Only checking one type of bracket'],
discussionPoints:['LIFO property of stacks','Matching pairs','Early termination'],
bruteForce:{approach:'Repeatedly scan the string and remove matching adjacent pairs until no more can be removed.',timeComplexity:'O(n²)',spaceComplexity:'O(n)',code:'while "()" in s or "[]" in s or "{}" in s:\n  s = s.replace("()", "").replace("[]", "").replace("{}", "")\nreturn len(s) == 0'},
optimal:{approach:'Use a stack. Push opening brackets. On closing brackets, check if the top of stack is the matching opener.',timeComplexity:'O(n)',spaceComplexity:'O(n)',code:'stack = []\nmatching = {")":"(", "]":"[", "}":"{"}\nfor c in s:\n  if c in matching:\n    if not stack or stack[-1] != matching[c]:\n      return False\n    stack.pop()\n  else:\n    stack.append(c)\nreturn len(stack) == 0'},
hints:['Think about which data structure processes things in reverse order.','A stack processes last-in-first-out.','Push opening brackets, pop on closing.','When you see a closing bracket, the top of stack should be its matching opener.','Don\'t forget: the stack must be empty at the end for the string to be valid.'],
edgeCases:['Empty string','Single bracket','Only opening brackets','Only closing brackets','Nested brackets of different types'],
followUps:['What if you only had one type of bracket?','How would you find the minimum number of brackets to remove to make it valid?','What about HTML tags?'],
solutionExplanation:'The stack naturally handles nesting. Each opening bracket waits on the stack until its matching closer appears. If a closer doesn\'t match the top of stack, the string is invalid.',
patternQuiz:{summary:'Check if a string of brackets is properly nested and matched.',correct:'stack',distractors:['hash-map','two-pointers','recursion']}},

{id:3,title:'Best Time to Buy and Sell Stock',difficulty:'easy',category:'arrays',patterns:['greedy'],cognitiveLoad:'low',recommendedTime:15,
statement:'You are given an array prices where prices[i] is the price of a given stock on the ith day.\n\nYou want to maximize your profit by choosing a single day to buy and a single day to sell in the future. Return the maximum profit. If no profit is possible, return 0.',
examples:[{input:'prices = [7,1,5,3,6,4]',output:'5',explanation:'Buy on day 2 (price=1), sell on day 5 (price=6), profit=5'}],
commonMistakes:['Trying to sell before buying','Not tracking the minimum price seen so far','Using nested loops when a single pass suffices'],
discussionPoints:['Greedy approach: track running minimum','Why we can\'t just find max and min','The constraint that buy must come before sell'],
bruteForce:{approach:'Check every pair where buy day < sell day.',timeComplexity:'O(n²)',spaceComplexity:'O(1)',code:'max_profit = 0\nfor i in range(len(prices)):\n  for j in range(i+1, len(prices)):\n    max_profit = max(max_profit, prices[j]-prices[i])\nreturn max_profit'},
optimal:{approach:'Single pass: track the minimum price seen so far. At each day, calculate profit if selling today and update the max.',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'min_price = float("inf")\nmax_profit = 0\nfor price in prices:\n  min_price = min(min_price, price)\n  max_profit = max(max_profit, price - min_price)\nreturn max_profit'},
hints:['You need to buy before you sell.','What if you tracked the cheapest price you\'ve seen so far?','At each price, your best profit is current price minus the cheapest price before it.','Single pass: update min_price and max_profit as you go.','Initialize min_price to infinity and max_profit to 0.'],
edgeCases:['Prices always decreasing','Only one price','All prices the same','Only two prices'],
followUps:['What if you could buy and sell multiple times?','What if there\'s a transaction fee?','What if you could short sell?'],
solutionExplanation:'The greedy insight: at any day, the best possible profit is today\'s price minus the lowest price we\'ve seen before today. By tracking the running minimum, we get an O(n) solution.',
patternQuiz:{summary:'Find the maximum profit from one buy-sell transaction on a stock price array.',correct:'greedy',distractors:['dp','two-pointers','sliding-window']}},

{id:4,title:'Maximum Depth of Binary Tree',difficulty:'easy',category:'trees',patterns:['dfs'],cognitiveLoad:'low',recommendedTime:10,
statement:'Given the root of a binary tree, return its maximum depth.\n\nA binary tree\'s maximum depth is the number of nodes along the longest path from the root node down to the farthest leaf node.',
examples:[{input:'root = [3,9,20,null,null,15,7]',output:'3',explanation:'The tree has depth 3'}],
commonMistakes:['Off-by-one error (counting edges vs nodes)','Not handling null root','Forgetting to take the max of left and right'],
discussionPoints:['DFS vs BFS approach','Recursive vs iterative','Base case handling'],
bruteForce:{approach:'Same as optimal — DFS recursion is already clean and efficient.',timeComplexity:'O(n)',spaceComplexity:'O(h) where h is height',code:'def maxDepth(root):\n  if not root:\n    return 0\n  return 1 + max(maxDepth(root.left), maxDepth(root.right))'},
optimal:{approach:'DFS recursion: depth of a node is 1 + max(depth of left, depth of right). Base case: null node has depth 0.',timeComplexity:'O(n)',spaceComplexity:'O(h)',code:'def maxDepth(root):\n  if not root:\n    return 0\n  return 1 + max(maxDepth(root.left), maxDepth(root.right))'},
hints:['Think recursively.','What\'s the depth of an empty tree?','The depth of a node is 1 plus the max depth of its children.','Base case: null returns 0.','return 1 + max(left_depth, right_depth)'],
edgeCases:['Empty tree (null root)','Single node','Completely skewed tree (like a linked list)','Balanced tree'],
followUps:['Can you solve it iteratively?','What about minimum depth?','How would you find the diameter of the tree?'],
solutionExplanation:'Classic DFS recursion. A null node has depth 0. For any other node, its depth equals 1 plus the maximum of its children\'s depths. This visits every node exactly once.',
patternQuiz:{summary:'Find the maximum depth (longest root-to-leaf path) of a binary tree.',correct:'dfs',distractors:['bfs','binary-search','two-pointers']}},

{id:5,title:'Climbing Stairs',difficulty:'easy',category:'dynamic-programming',patterns:['dp'],cognitiveLoad:'low',recommendedTime:15,
statement:'You are climbing a staircase. It takes n steps to reach the top. Each time you can either climb 1 or 2 steps. In how many distinct ways can you climb to the top?',
examples:[{input:'n = 3',output:'3',explanation:'1+1+1, 1+2, 2+1'},{input:'n = 2',output:'2',explanation:'1+1, 2'}],
commonMistakes:['Not recognizing it as Fibonacci','Using exponential recursion without memoization','Off-by-one on base cases'],
discussionPoints:['Fibonacci connection','Top-down vs bottom-up DP','Space optimization to O(1)'],
bruteForce:{approach:'Recursive: ways(n) = ways(n-1) + ways(n-2).',timeComplexity:'O(2^n)',spaceComplexity:'O(n)',code:'def climb(n):\n  if n <= 2: return n\n  return climb(n-1) + climb(n-2)'},
optimal:{approach:'Bottom-up DP with two variables (Fibonacci). ways[i] = ways[i-1] + ways[i-2].',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'def climb(n):\n  if n <= 2: return n\n  a, b = 1, 2\n  for i in range(3, n+1):\n    a, b = b, a + b\n  return b'},
hints:['How do you reach step n? From step n-1 or n-2.','This is a recurrence relation.','It\'s the Fibonacci sequence!','You don\'t need an array — just two variables.','Bottom-up: track the previous two values.'],
edgeCases:['n = 0','n = 1','Large n (efficiency matters)'],
followUps:['What if you could take 1, 2, or 3 steps?','What if certain steps are blocked?','Can you solve it in O(log n) with matrix exponentiation?'],
solutionExplanation:'To reach step n, you either came from step n-1 (took 1 step) or step n-2 (took 2 steps). So ways(n) = ways(n-1) + ways(n-2). This is exactly the Fibonacci sequence, solvable in O(n) time and O(1) space.',
patternQuiz:{summary:'Count the number of distinct ways to climb n stairs taking 1 or 2 steps at a time.',correct:'dp',distractors:['greedy','recursion','backtracking']}},

{id:6,title:'Reverse Linked List',difficulty:'easy',category:'linked-lists',patterns:['linked-list'],cognitiveLoad:'low',recommendedTime:15,
statement:'Given the head of a singly linked list, reverse the list, and return the reversed list.',
examples:[{input:'head = [1,2,3,4,5]',output:'[5,4,3,2,1]',explanation:''}],
commonMistakes:['Losing the reference to the next node','Not updating the head pointer','Forgetting to set the original head\'s next to null'],
discussionPoints:['Iterative vs recursive','In-place reversal','Pointer manipulation'],
bruteForce:{approach:'Store values in array, then create new reversed list.',timeComplexity:'O(n)',spaceComplexity:'O(n)',code:'values = []\nwhile head:\n  values.append(head.val)\n  head = head.next\n# rebuild in reverse'},
optimal:{approach:'Iterative: use three pointers (prev, curr, next) to reverse links in place.',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'prev = None\ncurr = head\nwhile curr:\n  next_node = curr.next\n  curr.next = prev\n  prev = curr\n  curr = next_node\nreturn prev'},
hints:['You need to change where each node points.','Keep track of the previous node.','Save the next node before changing the link.','Three pointers: prev, curr, next.','After the loop, prev is the new head.'],
edgeCases:['Empty list','Single node','Two nodes'],
followUps:['Can you do it recursively?','What about reversing only a portion (between positions m and n)?','Reverse in groups of k?'],
solutionExplanation:'The iterative approach uses three pointers. At each step: save next, point current backwards to prev, advance prev and current. When current is null, prev is the new head.',
patternQuiz:{summary:'Reverse a singly linked list and return the new head.',correct:'linked-list',distractors:['two-pointers','stack','recursion']}},

{id:7,title:'Binary Search',difficulty:'easy',category:'searching',patterns:['binary-search'],cognitiveLoad:'low',recommendedTime:10,
statement:'Given a sorted array of integers nums and an integer target, return the index of the target if found, or -1 if not found. You must write an algorithm with O(log n) runtime complexity.',
examples:[{input:'nums = [-1,0,3,5,9,12], target = 9',output:'4',explanation:''},{input:'nums = [-1,0,3,5,9,12], target = 2',output:'-1',explanation:''}],
commonMistakes:['Off-by-one errors in left/right boundaries','Infinite loop from incorrect mid calculation','Not handling empty array'],
discussionPoints:['Left-closed right-closed vs left-closed right-open','Integer overflow in mid calculation','Invariant maintenance'],
bruteForce:{approach:'Linear scan through the array.',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'for i in range(len(nums)):\n  if nums[i] == target:\n    return i\nreturn -1'},
optimal:{approach:'Binary search: maintain left and right pointers, check mid, narrow by half each step.',timeComplexity:'O(log n)',spaceComplexity:'O(1)',code:'left, right = 0, len(nums) - 1\nwhile left <= right:\n  mid = left + (right - left) // 2\n  if nums[mid] == target:\n    return mid\n  elif nums[mid] < target:\n    left = mid + 1\n  else:\n    right = mid - 1\nreturn -1'},
hints:['The array is sorted — exploit that.','Compare target to the middle element.','If target is larger, search the right half.','Use left + (right-left)//2 to avoid overflow.','Loop while left <= right.'],
edgeCases:['Empty array','Single element','Target at start or end','Target not present'],
followUps:['Find the first occurrence of target','Find the insertion position','Search in a rotated sorted array'],
solutionExplanation:'Binary search halves the search space at each step by comparing the target to the middle element. The key is maintaining correct boundaries and using left + (right-left)//2 to prevent integer overflow.',
patternQuiz:{summary:'Find a target value\'s index in a sorted array efficiently.',correct:'binary-search',distractors:['two-pointers','hash-map','dfs']}},

{id:8,title:'Single Number',difficulty:'easy',category:'bit-manipulation',patterns:['bit-manipulation'],cognitiveLoad:'low',recommendedTime:10,
statement:'Given a non-empty array of integers nums, every element appears twice except for one. Find that single one. You must implement a solution with linear runtime and constant extra space.',
examples:[{input:'nums = [2,2,1]',output:'1',explanation:''},{input:'nums = [4,1,2,1,2]',output:'4',explanation:''}],
commonMistakes:['Using a hash set (O(n) space, not constant)','Not knowing XOR properties','Trying to sort (O(n log n), not linear)'],
discussionPoints:['XOR properties: a^a=0, a^0=a','Why sorting doesn\'t meet the constraints','Math approach: 2*sum(set) - sum(array)'],
bruteForce:{approach:'Use a hash set: add if not seen, remove if already there. The remaining element is the answer.',timeComplexity:'O(n)',spaceComplexity:'O(n)',code:'seen = set()\nfor n in nums:\n  if n in seen: seen.remove(n)\n  else: seen.add(n)\nreturn seen.pop()'},
optimal:{approach:'XOR all elements. Pairs cancel out (a^a=0), leaving only the single number.',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'result = 0\nfor n in nums:\n  result ^= n\nreturn result'},
hints:['Think about a bitwise operation that cancels duplicates.','XOR has a useful property: a ^ a = 0.','Also: a ^ 0 = a, and XOR is commutative/associative.','XOR all elements together.','Pairs cancel out, leaving the single number.'],
edgeCases:['Array with one element','Large numbers','Negative numbers'],
followUps:['What if every element appeared three times except one?','What if there were two single numbers?'],
solutionExplanation:'XOR is the key. Since a^a=0 and a^0=a, XORing all elements causes every pair to cancel out, leaving only the unpaired element. This is O(n) time, O(1) space.',
patternQuiz:{summary:'Find the one element that appears only once in an array where every other element appears twice, using constant space.',correct:'bit-manipulation',distractors:['hash-map','sorting','two-pointers']}},

{id:9,title:'Longest Substring Without Repeating Characters',difficulty:'medium',category:'strings',patterns:['sliding-window'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given a string s, find the length of the longest substring without repeating characters.',
examples:[{input:'s = "abcabcbb"',output:'3',explanation:'The answer is "abc"'},{input:'s = "bbbbb"',output:'1',explanation:'The answer is "b"'}],
commonMistakes:['Confusing substring with subsequence','Not moving the left pointer correctly on duplicate','Using a set instead of a map (map stores positions for faster jumps)'],
discussionPoints:['Sliding window technique','Set vs map for tracking characters','When to shrink the window'],
bruteForce:{approach:'Check every substring for uniqueness.',timeComplexity:'O(n³)',spaceComplexity:'O(n)',code:'max_len = 0\nfor i in range(len(s)):\n  for j in range(i, len(s)):\n    if len(set(s[i:j+1])) == j-i+1:\n      max_len = max(max_len, j-i+1)\nreturn max_len'},
optimal:{approach:'Sliding window with a set/map. Expand right, shrink left when duplicate found.',timeComplexity:'O(n)',spaceComplexity:'O(min(n, alphabet))',code:'char_index = {}\nleft = 0\nmax_len = 0\nfor right, c in enumerate(s):\n  if c in char_index and char_index[c] >= left:\n    left = char_index[c] + 1\n  char_index[c] = right\n  max_len = max(max_len, right - left + 1)\nreturn max_len'},
hints:['Think about maintaining a window of unique characters.','Use two pointers: left and right.','Expand right; when you hit a duplicate, move left past the previous occurrence.','A hash map mapping char → last index lets you jump left directly.','Update max_len at each step.'],
edgeCases:['Empty string','All same characters','All unique characters','Single character'],
followUps:['What if you need the actual substring, not just length?','What about longest with at most k repeating characters?'],
solutionExplanation:'The sliding window approach maintains a window [left, right] of unique characters. When a duplicate is found, left jumps to one past the previous occurrence. A hash map stores each character\'s last-seen index for O(1) jump computation.',
patternQuiz:{summary:'Find the length of the longest substring where no character repeats.',correct:'sliding-window',distractors:['two-pointers','dp','hash-map']}},

{id:10,title:'3Sum',difficulty:'medium',category:'arrays',patterns:['two-pointers'],cognitiveLoad:'medium',recommendedTime:25,
statement:'Given an integer array nums, return all the triplets [nums[i], nums[j], nums[k]] such that i != j, i != k, and j != k, and nums[i] + nums[j] + nums[k] == 0.\n\nThe solution set must not contain duplicate triplets.',
examples:[{input:'nums = [-1,0,1,2,-1,-4]',output:'[[-1,-1,2],[-1,0,1]]',explanation:''}],
commonMistakes:['Not handling duplicates (returning duplicate triplets)','Not sorting first','Using three nested loops O(n³)'],
discussionPoints:['Sorting enables two-pointer technique','Duplicate skipping strategy','Reducing from 3-sum to 2-sum'],
bruteForce:{approach:'Three nested loops checking all triplets.',timeComplexity:'O(n³)',spaceComplexity:'O(1)',code:'result = set()\nfor i for j for k:\n  if nums[i]+nums[j]+nums[k]==0:\n    result.add(tuple(sorted([nums[i],nums[j],nums[k]])))'},
optimal:{approach:'Sort array. For each element, use two pointers on the remaining array to find pairs that sum to its negation. Skip duplicates.',timeComplexity:'O(n²)',spaceComplexity:'O(1) extra',code:'nums.sort()\nresult = []\nfor i in range(len(nums)-2):\n  if i > 0 and nums[i] == nums[i-1]: continue\n  lo, hi = i+1, len(nums)-1\n  while lo < hi:\n    total = nums[i] + nums[lo] + nums[hi]\n    if total < 0: lo += 1\n    elif total > 0: hi -= 1\n    else:\n      result.append([nums[i],nums[lo],nums[hi]])\n      while lo<hi and nums[lo]==nums[lo+1]: lo+=1\n      while lo<hi and nums[hi]==nums[hi-1]: hi-=1\n      lo+=1; hi-=1'},
hints:['Sorting the array opens up new approaches.','For each number, you need two others that sum to its negation.','Two pointers from both ends of the remaining sorted array.','Skip duplicates for both the outer loop and inner pointers.','Time: O(n²) total.'],
edgeCases:['Array with fewer than 3 elements','All zeros','All positive or all negative','Many duplicates'],
followUps:['What about 4Sum?','What about closest 3Sum?','Can you generalize to kSum?'],
solutionExplanation:'Sort first. Then for each element at index i, use two pointers (lo=i+1, hi=end) to find pairs summing to -nums[i]. Skip duplicates at both levels to avoid duplicate triplets.',
patternQuiz:{summary:'Find all unique triplets in an array that sum to zero.',correct:'two-pointers',distractors:['hash-map','binary-search','sliding-window']}},

{id:11,title:'Merge Intervals',difficulty:'medium',category:'intervals',patterns:['merge-intervals'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given an array of intervals where intervals[i] = [starti, endi], merge all overlapping intervals, and return an array of the non-overlapping intervals that cover all the intervals in the input.',
examples:[{input:'intervals = [[1,3],[2,6],[8,10],[15,18]]',output:'[[1,6],[8,10],[15,18]]',explanation:'Intervals [1,3] and [2,6] overlap, merged to [1,6]'}],
commonMistakes:['Forgetting to sort by start time first','Not handling contained intervals','Edge case where intervals touch but don\'t overlap'],
discussionPoints:['Sort by start time first','Merge condition: current start <= previous end','Updating the end with max(prev end, current end)'],
bruteForce:{approach:'Sort, then repeatedly scan for overlaps (multiple passes).',timeComplexity:'O(n² log n)',spaceComplexity:'O(n)',code:'Sort and repeatedly merge until no changes'},
optimal:{approach:'Sort by start time. Iterate and merge: if current interval overlaps the last merged interval, extend it; otherwise add as new.',timeComplexity:'O(n log n)',spaceComplexity:'O(n)',code:'intervals.sort(key=lambda x: x[0])\nmerged = [intervals[0]]\nfor start, end in intervals[1:]:\n  if start <= merged[-1][1]:\n    merged[-1][1] = max(merged[-1][1], end)\n  else:\n    merged.append([start, end])\nreturn merged'},
hints:['What if you sorted the intervals first?','After sorting by start time, overlapping intervals are adjacent.','Compare each interval\'s start to the previous merged interval\'s end.','If overlapping, extend the end; otherwise start a new merged interval.','Use max() for the new end to handle contained intervals.'],
edgeCases:['Single interval','All intervals overlap','No intervals overlap','Intervals that touch at endpoints'],
followUps:['Insert a new interval into a sorted list of non-overlapping intervals','Find the minimum number of intervals to remove to make the rest non-overlapping'],
solutionExplanation:'Sorting by start time ensures overlapping intervals are adjacent. Then a single pass merges them: if the current start is within the previous end, extend the previous end (using max). Otherwise, start a new group.',
patternQuiz:{summary:'Given a list of intervals, merge all that overlap into non-overlapping intervals.',correct:'merge-intervals',distractors:['two-pointers','greedy','sorting']}},

{id:12,title:'Number of Islands',difficulty:'medium',category:'graphs',patterns:['dfs','bfs'],cognitiveLoad:'medium',recommendedTime:25,
statement:'Given an m x n 2D binary grid which represents a map of \'1\'s (land) and \'0\'s (water), return the number of islands.\n\nAn island is surrounded by water and is formed by connecting adjacent lands horizontally or vertically.',
examples:[{input:'grid = [["1","1","0"],["1","1","0"],["0","0","1"]]',output:'2',explanation:''}],
commonMistakes:['Not marking visited cells (infinite loop)','Forgetting diagonal is not adjacent','Modifying the original grid vs using a visited set'],
discussionPoints:['DFS vs BFS for flood fill','Marking visited in-place vs separate set','Union-Find alternative'],
bruteForce:{approach:'Same as optimal — DFS/BFS is already the standard approach.',timeComplexity:'O(m×n)',spaceComplexity:'O(m×n)',code:'(Same as optimal)'},
optimal:{approach:'Iterate through grid. When you find a \'1\', increment count and DFS/BFS to mark all connected land as visited.',timeComplexity:'O(m×n)',spaceComplexity:'O(m×n) for recursion stack/queue',code:'def numIslands(grid):\n  count = 0\n  for i in range(len(grid)):\n    for j in range(len(grid[0])):\n      if grid[i][j] == "1":\n        count += 1\n        dfs(grid, i, j)\n  return count\n\ndef dfs(grid, i, j):\n  if i<0 or j<0 or i>=len(grid) or j>=len(grid[0]) or grid[i][j]!="1":\n    return\n  grid[i][j] = "0"\n  for di,dj in [(1,0),(-1,0),(0,1),(0,-1)]:\n    dfs(grid, i+di, j+dj)'},
hints:['Think of this as a graph problem — cells are nodes, adjacent cells are edges.','When you find land, you need to explore all connected land.','DFS or BFS can do this exploration (flood fill).','Mark visited cells so you don\'t count them again.','Each time you start a new DFS/BFS from an unvisited \'1\', that\'s a new island.'],
edgeCases:['Empty grid','All water','All land','Single cell'],
followUps:['What about the area of the largest island?','What if islands can connect diagonally?','Surrounded regions problem?'],
solutionExplanation:'Classic flood fill. Scan the grid; when you find an unvisited \'1\', that\'s a new island. DFS/BFS from that cell to mark all connected \'1\'s as visited. The total number of times you start a new DFS/BFS is the number of islands.',
patternQuiz:{summary:'Count distinct groups of connected \'1\'s in a 2D grid of 0s and 1s.',correct:'dfs',distractors:['bfs','union-find','two-pointers']}},

{id:13,title:'Coin Change',difficulty:'medium',category:'dynamic-programming',patterns:['dp'],cognitiveLoad:'medium',recommendedTime:25,
statement:'You are given an integer array coins representing coin denominations and an integer amount representing a total amount of money.\n\nReturn the fewest number of coins needed to make up that amount. If it cannot be made up, return -1. You have an infinite supply of each coin.',
examples:[{input:'coins = [1,5,11], amount = 15',output:'3',explanation:'5+5+5 = 15 (not 11+1+1+1+1 which is 5 coins — greedy fails!)'}],
commonMistakes:['Using greedy (take largest coin first) — doesn\'t always work','Not initializing DP array with infinity','Off-by-one in DP range'],
discussionPoints:['Why greedy fails for this problem','Bottom-up DP tabulation','The recurrence: dp[i] = min(dp[i-c] + 1) for each coin c'],
bruteForce:{approach:'Recursive: try every coin at each step.',timeComplexity:'O(amount^n) where n is number of coins',spaceComplexity:'O(amount)',code:'def coinChange(coins, amount):\n  if amount == 0: return 0\n  if amount < 0: return -1\n  best = float("inf")\n  for c in coins:\n    sub = coinChange(coins, amount-c)\n    if sub >= 0: best = min(best, sub+1)\n  return best if best < float("inf") else -1'},
optimal:{approach:'Bottom-up DP. dp[i] = minimum coins to make amount i. For each amount from 1 to target, try each coin.',timeComplexity:'O(amount × coins)',spaceComplexity:'O(amount)',code:'def coinChange(coins, amount):\n  dp = [float("inf")] * (amount + 1)\n  dp[0] = 0\n  for i in range(1, amount + 1):\n    for c in coins:\n      if c <= i:\n        dp[i] = min(dp[i], dp[i-c] + 1)\n  return dp[amount] if dp[amount] != float("inf") else -1'},
hints:['Greedy won\'t work here. Think about why.','This is a classic DP problem.','Define dp[i] = fewest coins to make amount i.','For each amount i, try subtracting each coin c: dp[i] = min(dp[i-c] + 1).','Base case: dp[0] = 0. Initialize everything else to infinity.'],
edgeCases:['Amount is 0','Amount is impossible to make','Single coin denomination','Very large amount'],
followUps:['What if you want the number of ways to make the amount?','What if each coin can only be used once?','What if you need to return the actual coins used?'],
solutionExplanation:'Greedy fails because the largest coin isn\'t always optimal (e.g., coins [1,5,11], amount 15: greedy gives 11+1+1+1+1=5 coins, but 5+5+5=3 coins). DP considers all possibilities: dp[i] is the minimum over dp[i-c]+1 for each coin c.',
patternQuiz:{summary:'Find the minimum number of coins needed to make a given amount from given denominations.',correct:'dp',distractors:['greedy','backtracking','bfs']}},

{id:14,title:'Top K Frequent Elements',difficulty:'medium',category:'arrays',patterns:['heap','hash-map'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given an integer array nums and an integer k, return the k most frequent elements. You may return the answer in any order.',
examples:[{input:'nums = [1,1,1,2,2,3], k = 2',output:'[1,2]',explanation:''}],
commonMistakes:['Not considering bucket sort approach','Using full sort when partial sort suffices','Misunderstanding heap operations'],
discussionPoints:['Count frequencies with hash map','Min-heap of size k vs full sort','Bucket sort for O(n) solution'],
bruteForce:{approach:'Count frequencies, sort by frequency, take top k.',timeComplexity:'O(n log n)',spaceComplexity:'O(n)',code:'from collections import Counter\nreturn [x for x,_ in Counter(nums).most_common(k)]'},
optimal:{approach:'Count frequencies, then use a min-heap of size k or bucket sort.',timeComplexity:'O(n log k) with heap, O(n) with bucket sort',spaceComplexity:'O(n)',code:'from collections import Counter\nimport heapq\ncount = Counter(nums)\nreturn heapq.nlargest(k, count.keys(), key=count.get)\n\n# Bucket sort O(n):\nbuckets = [[] for _ in range(len(nums)+1)]\nfor num, freq in count.items():\n  buckets[freq].append(num)\nresult = []\nfor i in range(len(buckets)-1, -1, -1):\n  result.extend(buckets[i])\n  if len(result) >= k: break\nreturn result[:k]'},
hints:['First step: count how often each element appears.','A hash map gives you frequency counts in O(n).','Now you need the top k. Full sort is O(n log n), but you can do better.','A min-heap of size k gives O(n log k).','Bucket sort using frequency as index gives O(n).'],
edgeCases:['k equals the number of unique elements','All elements are the same','k = 1'],
followUps:['What about the k least frequent?','What if the data is streaming?','What if there are ties in frequency?'],
solutionExplanation:'Count frequencies with a hash map (O(n)). Then either use a min-heap of size k (O(n log k)) or bucket sort where index = frequency (O(n)). Bucket sort is asymptotically optimal.',
patternQuiz:{summary:'Return the k elements that appear most frequently in an array.',correct:'heap',distractors:['binary-search','sorting','two-pointers']}},

{id:15,title:'Validate Binary Search Tree',difficulty:'medium',category:'trees',patterns:['dfs'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given the root of a binary tree, determine if it is a valid binary search tree (BST).\n\nA valid BST has: left subtree only contains nodes less than the node, right subtree only contains nodes greater than the node, and both left and right subtrees must also be BSTs.',
examples:[{input:'root = [2,1,3]',output:'true',explanation:''},{input:'root = [5,1,4,null,null,3,6]',output:'false',explanation:'Node 4 is in right subtree of 5 but is less than 5'}],
commonMistakes:['Only checking immediate children instead of entire subtrees','Not passing min/max bounds correctly','Not handling equal values'],
discussionPoints:['Recursive with min/max bounds','Inorder traversal should produce sorted sequence','Handling integer overflow with bounds'],
bruteForce:{approach:'Inorder traversal, check if result is sorted.',timeComplexity:'O(n)',spaceComplexity:'O(n)',code:'def isValid(root):\n  arr = []\n  inorder(root, arr)\n  return arr == sorted(set(arr))'},
optimal:{approach:'Recursive validation with min/max bounds. Each node must be within a valid range.',timeComplexity:'O(n)',spaceComplexity:'O(h)',code:'def isValidBST(root, lo=float("-inf"), hi=float("inf")):\n  if not root: return True\n  if root.val <= lo or root.val >= hi:\n    return False\n  return isValidBST(root.left, lo, root.val) and \\\n         isValidBST(root.right, root.val, hi)'},
hints:['Just checking left < node < right for each node is not enough.','Every node in the left subtree must be less than the current node.','Pass down valid ranges (min, max) as you recurse.','Left child: update upper bound. Right child: update lower bound.','Base case: null node is valid.'],
edgeCases:['Empty tree','Single node','All left or all right children','Duplicate values'],
followUps:['What about finding the kth smallest element?','How about recovering a BST where two nodes are swapped?'],
solutionExplanation:'The key insight is that each node must fall within a valid range. The root can be anything. Its left child must be less than it (upper bound = root.val). Its right child must be greater (lower bound = root.val). Pass these bounds recursively.',
patternQuiz:{summary:'Determine if a binary tree satisfies the BST property where left < node < right for all subtrees.',correct:'dfs',distractors:['bfs','binary-search','two-pointers']}},

{id:16,title:'Subsets',difficulty:'medium',category:'recursion',patterns:['backtracking'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given an integer array nums of unique elements, return all possible subsets (the power set). The solution set must not contain duplicate subsets.',
examples:[{input:'nums = [1,2,3]',output:'[[],[1],[2],[1,2],[3],[1,3],[2,3],[1,2,3]]',explanation:''}],
commonMistakes:['Generating duplicate subsets','Not including the empty set','Modifying the current subset without proper backtracking'],
discussionPoints:['Backtracking template','Include/exclude decision tree','Iterative approach: build from previous subsets','Bit manipulation approach'],
bruteForce:{approach:'Iterative: start with [[]], for each number add it to all existing subsets.',timeComplexity:'O(n × 2^n)',spaceComplexity:'O(n × 2^n)',code:'result = [[]]\nfor num in nums:\n  result += [s + [num] for s in result]\nreturn result'},
optimal:{approach:'Backtracking: at each element, choose to include or exclude it.',timeComplexity:'O(n × 2^n)',spaceComplexity:'O(n) recursion depth',code:'result = []\ndef backtrack(start, current):\n  result.append(current[:])\n  for i in range(start, len(nums)):\n    current.append(nums[i])\n    backtrack(i + 1, current)\n    current.pop()\nbacktrack(0, [])\nreturn result'},
hints:['There are 2^n subsets total.','Think of it as a binary decision: include or exclude each element.','Backtracking: add current element, recurse, then remove it.','Start index prevents duplicates (don\'t go backwards).','Remember to add a copy of the current subset, not a reference.'],
edgeCases:['Empty array','Single element','Large array (exponential output)'],
followUps:['What if the array contains duplicates?','What about combinations of size k?','Permutations instead of subsets?'],
solutionExplanation:'Backtracking explores a decision tree. At each level, we choose which elements to include starting from a given index. We add the current subset to results at every node (not just leaves), giving us all 2^n subsets.',
patternQuiz:{summary:'Generate all possible subsets of a set of unique integers.',correct:'backtracking',distractors:['dfs','dp','two-pointers']}},

{id:17,title:'Container With Most Water',difficulty:'medium',category:'arrays',patterns:['two-pointers'],cognitiveLoad:'medium',recommendedTime:20,
statement:'You are given an integer array height of length n. There are n vertical lines drawn such that the two endpoints of the ith line are (i, 0) and (i, height[i]).\n\nFind two lines that together with the x-axis form a container, such that the container contains the most water. Return the maximum amount of water a container can store.',
examples:[{input:'height = [1,8,6,2,5,4,8,3,7]',output:'49',explanation:'Lines at indices 1 and 8 with heights 8 and 7'}],
commonMistakes:['Moving the taller pointer instead of the shorter one','Confusing this with trapping rain water','Not understanding why the greedy two-pointer works'],
discussionPoints:['Why moving the shorter line is always correct','Width vs height trade-off','Proof of correctness for two-pointer approach'],
bruteForce:{approach:'Check every pair of lines.',timeComplexity:'O(n²)',spaceComplexity:'O(1)',code:'max_area = 0\nfor i in range(len(height)):\n  for j in range(i+1, len(height)):\n    area = min(height[i], height[j]) * (j - i)\n    max_area = max(max_area, area)\nreturn max_area'},
optimal:{approach:'Two pointers from both ends. Always move the shorter pointer inward. Area = min(h[l], h[r]) × (r-l).',timeComplexity:'O(n)',spaceComplexity:'O(1)',code:'left, right = 0, len(height) - 1\nmax_area = 0\nwhile left < right:\n  area = min(height[left], height[right]) * (right - left)\n  max_area = max(max_area, area)\n  if height[left] < height[right]:\n    left += 1\n  else:\n    right -= 1\nreturn max_area'},
hints:['Start with the widest container (both ends).','The area is limited by the shorter line.','Moving the taller line can only decrease width without increasing height.','So always move the shorter line inward — it might find a taller line.','This greedy choice is provably optimal.'],
edgeCases:['Two elements only','All same height','Strictly increasing or decreasing'],
followUps:['Trapping Rain Water (different problem!)','What if lines had width?'],
solutionExplanation:'Start with pointers at both ends (maximum width). The bottleneck is always the shorter line. Moving the taller line inward can only reduce width without possibility of increasing the min height. So we greedily move the shorter line, hoping to find a taller one.',
patternQuiz:{summary:'Find two vertical lines that, with the x-axis, form a container holding the most water.',correct:'two-pointers',distractors:['greedy','sliding-window','binary-search']}},

{id:18,title:'Course Schedule',difficulty:'medium',category:'graphs',patterns:['topological-sort'],cognitiveLoad:'high',recommendedTime:30,
statement:'There are numCourses courses labeled from 0 to numCourses-1. You are given an array prerequisites where prerequisites[i] = [ai, bi] indicates that you must take course bi first before taking course ai.\n\nReturn true if you can finish all courses. Otherwise, return false.',
examples:[{input:'numCourses = 2, prerequisites = [[1,0]]',output:'true',explanation:'Take course 0 then course 1'},{input:'numCourses = 2, prerequisites = [[1,0],[0,1]]',output:'false',explanation:'Circular dependency'}],
commonMistakes:['Not detecting cycles properly','Using simple visited set instead of tracking recursion stack','Not building the adjacency list correctly'],
discussionPoints:['This is cycle detection in a directed graph','Topological sort: BFS (Kahn\'s) vs DFS','In-degree tracking for BFS approach'],
bruteForce:{approach:'DFS with cycle detection (three-color marking).',timeComplexity:'O(V+E)',spaceComplexity:'O(V+E)',code:'(Same complexity as optimal — both are graph traversals)'},
optimal:{approach:'BFS topological sort (Kahn\'s algorithm): start with nodes of in-degree 0, process layer by layer.',timeComplexity:'O(V+E)',spaceComplexity:'O(V+E)',code:'from collections import deque\ndef canFinish(numCourses, prerequisites):\n  graph = [[] for _ in range(numCourses)]\n  indegree = [0] * numCourses\n  for a, b in prerequisites:\n    graph[b].append(a)\n    indegree[a] += 1\n  queue = deque(i for i in range(numCourses) if indegree[i]==0)\n  count = 0\n  while queue:\n    node = queue.popleft()\n    count += 1\n    for nei in graph[node]:\n      indegree[nei] -= 1\n      if indegree[nei] == 0:\n        queue.append(nei)\n  return count == numCourses'},
hints:['This is a graph problem. What does a cycle mean here?','If there\'s a cycle, it\'s impossible to complete all courses.','Topological sort: process nodes with no prerequisites first.','BFS version (Kahn\'s): start with in-degree 0 nodes, reduce neighbors\' in-degrees.','If you process all nodes, no cycle exists. If some remain, there\'s a cycle.'],
edgeCases:['No prerequisites','Self-referencing prerequisite','Multiple connected components','Single course'],
followUps:['Return the actual ordering (Course Schedule II)','What about minimum semesters to complete all courses?','What if some courses are optional?'],
solutionExplanation:'Model courses as a directed graph. A cycle means impossible prerequisites. Kahn\'s algorithm (BFS topological sort) starts from nodes with no prerequisites (in-degree 0). Process them, reduce neighbors\' in-degrees. If all nodes are processed, no cycle exists.',
patternQuiz:{summary:'Determine if all courses can be completed given a list of prerequisite pairs (detect if a directed graph has a cycle).',correct:'topological-sort',distractors:['dfs','bfs','union-find']}},

{id:19,title:'Word Search',difficulty:'medium',category:'recursion',patterns:['backtracking','dfs'],cognitiveLoad:'high',recommendedTime:25,
statement:'Given an m x n grid of characters board and a string word, return true if word exists in the grid.\n\nThe word can be constructed from letters of sequentially adjacent cells (horizontally or vertically). The same cell may not be used more than once.',
examples:[{input:'board = [["A","B","C","E"],["S","F","C","S"],["A","D","E","E"]], word = "ABCCED"',output:'true',explanation:''}],
commonMistakes:['Not restoring the cell after backtracking','Checking diagonals (only horizontal/vertical)','Not handling the starting position — need to try every cell'],
discussionPoints:['DFS backtracking on a grid','Marking visited cells','Time complexity analysis'],
bruteForce:{approach:'Same as optimal — backtracking is the standard approach.',timeComplexity:'O(m×n×4^L) where L is word length',spaceComplexity:'O(L) recursion depth',code:'(Same as optimal)'},
optimal:{approach:'For each cell matching word[0], start DFS backtracking. Mark cells as visited, explore 4 directions, unmark on backtrack.',timeComplexity:'O(m×n×4^L)',spaceComplexity:'O(L)',code:'def exist(board, word):\n  rows, cols = len(board), len(board[0])\n  def dfs(r, c, idx):\n    if idx == len(word): return True\n    if r<0 or c<0 or r>=rows or c>=cols: return False\n    if board[r][c] != word[idx]: return False\n    temp = board[r][c]\n    board[r][c] = "#"\n    found = any(dfs(r+dr,c+dc,idx+1) for dr,dc in [(0,1),(0,-1),(1,0),(-1,0)])\n    board[r][c] = temp\n    return found\n  return any(dfs(r,c,0) for r in range(rows) for c in range(cols))'},
hints:['Try starting the search from every cell.','DFS: match one character at a time, explore 4 neighbors.','Mark the current cell as visited to avoid reuse.','Backtrack: restore the cell after exploring all paths from it.','Return true as soon as you find one valid path.'],
edgeCases:['Word longer than grid size','Single character word','Word not in grid','Same letter used multiple times in word'],
followUps:['What if you need to find all occurrences?','Word Search II with multiple words (use Trie)','What about diagonal movement?'],
solutionExplanation:'Backtracking on a grid. Try every cell as a starting point. DFS matches one character at a time. Mark cells as visited (overwrite with special char), explore 4 directions, then restore. Key: backtracking (restoring state) prevents incorrect results.',
patternQuiz:{summary:'Determine if a word can be formed by following adjacent cells in a 2D character grid without reusing cells.',correct:'backtracking',distractors:['dfs','bfs','trie']}},

{id:20,title:'Implement Trie',difficulty:'medium',category:'tries',patterns:['trie'],cognitiveLoad:'high',recommendedTime:25,
statement:'Implement a trie (prefix tree) with these methods:\n- insert(word): inserts a word into the trie\n- search(word): returns true if the word is in the trie\n- startsWith(prefix): returns true if any word in the trie starts with the given prefix',
examples:[{input:'insert("apple"), search("apple"), search("app"), startsWith("app")',output:'null, true, false, true',explanation:''}],
commonMistakes:['Not distinguishing between a prefix and a complete word','Not initializing children correctly','Confusing search and startsWith'],
discussionPoints:['Trie node structure: children map + isEnd flag','Time complexity per operation: O(word length)','Space vs hash set trade-off','Use cases: autocomplete, spell check'],
bruteForce:{approach:'Use a list/set of words and iterate for each operation.',timeComplexity:'O(n×m) for search where n=word count, m=length',spaceComplexity:'O(total characters)',code:'words = set()\ndef search(word): return word in words\ndef startsWith(prefix): return any(w.startswith(prefix) for w in words)'},
optimal:{approach:'Trie with nodes containing children map and isEnd boolean.',timeComplexity:'O(m) per operation where m = word length',spaceComplexity:'O(total characters)',code:'class TrieNode:\n  def __init__(self):\n    self.children = {}\n    self.is_end = False\n\nclass Trie:\n  def __init__(self):\n    self.root = TrieNode()\n  \n  def insert(self, word):\n    node = self.root\n    for c in word:\n      if c not in node.children:\n        node.children[c] = TrieNode()\n      node = node.children[c]\n    node.is_end = True\n  \n  def search(self, word):\n    node = self._find(word)\n    return node is not None and node.is_end\n  \n  def startsWith(self, prefix):\n    return self._find(prefix) is not None\n  \n  def _find(self, word):\n    node = self.root\n    for c in word:\n      if c not in node.children: return None\n      node = node.children[c]\n    return node'},
hints:['Each node represents a character and has children for next characters.','A node needs a boolean flag: is this the end of a complete word?','insert: create nodes along the path, mark the last as end.','search: traverse the path, check that the final node exists AND is marked as end.','startsWith: same as search but don\'t check the isEnd flag.'],
edgeCases:['Empty string','Single character words','Words that are prefixes of other words','Search before any insert'],
followUps:['Add a delete method','Wildcard search (. matches any character)','Auto-complete suggestions','Count words with a given prefix'],
solutionExplanation:'A trie stores strings character by character in a tree. Each node has a map of children (char → node) and a boolean isEnd. Insert walks down creating nodes. Search walks down and checks isEnd. StartsWith walks down without checking isEnd.',
patternQuiz:{summary:'Design a data structure that efficiently inserts words and searches for exact matches or prefix matches.',correct:'trie',distractors:['hash-map','binary-search','dfs']}},

{id:21,title:'Longest Increasing Subsequence',difficulty:'medium',category:'dynamic-programming',patterns:['dp','binary-search'],cognitiveLoad:'high',recommendedTime:30,
statement:'Given an integer array nums, return the length of the longest strictly increasing subsequence.',
examples:[{input:'nums = [10,9,2,5,3,7,101,18]',output:'4',explanation:'The LIS is [2,3,7,101]'}],
commonMistakes:['Confusing subsequence with subarray','Not considering the O(n log n) patience sort approach','Incorrect DP transition'],
discussionPoints:['O(n²) DP approach','O(n log n) with binary search (patience sorting)','Reconstructing the actual subsequence'],
bruteForce:{approach:'DP: for each element, find the longest subsequence ending at that element.',timeComplexity:'O(n²)',spaceComplexity:'O(n)',code:'dp = [1] * len(nums)\nfor i in range(1, len(nums)):\n  for j in range(i):\n    if nums[j] < nums[i]:\n      dp[i] = max(dp[i], dp[j] + 1)\nreturn max(dp)'},
optimal:{approach:'Maintain a "tails" array. For each number, binary search for its position. If larger than all, append; otherwise replace the first element >= it.',timeComplexity:'O(n log n)',spaceComplexity:'O(n)',code:'import bisect\ndef lengthOfLIS(nums):\n  tails = []\n  for num in nums:\n    pos = bisect.bisect_left(tails, num)\n    if pos == len(tails):\n      tails.append(num)\n    else:\n      tails[pos] = num\n  return len(tails)'},
hints:['Think about what dp[i] means: longest subsequence ending at index i.','DP transition: dp[i] = max(dp[j]+1) for all j < i where nums[j] < nums[i].','The O(n²) DP works. Can we do better?','Maintain a sorted "tails" array and use binary search.','The length of the tails array at the end is the LIS length.'],
edgeCases:['Single element','Already sorted','Reverse sorted','All elements equal'],
followUps:['What is the actual subsequence (not just length)?','Number of longest increasing subsequences?','Longest common subsequence?'],
solutionExplanation:'The O(n²) DP defines dp[i] as the LIS ending at i. The O(n log n) approach maintains a "tails" array where tails[k] is the smallest tail element of all increasing subsequences of length k+1. Binary search places each new element optimally.',
patternQuiz:{summary:'Find the length of the longest strictly increasing subsequence in an array.',correct:'dp',distractors:['greedy','binary-search','two-pointers']}},

{id:22,title:'Group Anagrams',difficulty:'medium',category:'strings',patterns:['hash-map'],cognitiveLoad:'medium',recommendedTime:20,
statement:'Given an array of strings strs, group the anagrams together. You can return the answer in any order.',
examples:[{input:'strs = ["eat","tea","tan","ate","nat","bat"]',output:'[["bat"],["nat","tan"],["ate","eat","tea"]]',explanation:''}],
commonMistakes:['Using sorted string as key but not considering empty strings','Not handling single-character strings','Inefficient key generation'],
discussionPoints:['Sorted string as hash key','Character count tuple as alternative key','Time complexity trade-offs between approaches'],
bruteForce:{approach:'Compare every pair of strings to check if they are anagrams.',timeComplexity:'O(n² × k)',spaceComplexity:'O(nk)',code:'Compare each pair using sorted or char counts'},
optimal:{approach:'Use sorted string as hash map key. All anagrams have the same sorted form.',timeComplexity:'O(n × k log k) where k = max string length',spaceComplexity:'O(nk)',code:'from collections import defaultdict\ndef groupAnagrams(strs):\n  groups = defaultdict(list)\n  for s in strs:\n    key = "".join(sorted(s))\n    groups[key].append(s)\n  return list(groups.values())'},
hints:['What do all anagrams have in common?','They have the same characters in the same quantities.','Sorting a string gives a canonical form shared by all its anagrams.','Use the sorted string as a hash map key.','Alternative: use character count tuple as key for O(nk) time.'],
edgeCases:['Empty strings','Single character strings','All strings are anagrams of each other','No anagrams exist'],
followUps:['What if strings can be very long?','Character count key for O(nk) instead of O(nk log k)?','Find anagram pairs instead of groups?'],
solutionExplanation:'Anagrams are strings with identical character compositions. Sorting gives a canonical representation: all anagrams sort to the same string. Using this as a hash map key groups them efficiently.',
patternQuiz:{summary:'Group strings together if they are anagrams of each other.',correct:'hash-map',distractors:['sorting','two-pointers','trie']}},

{id:23,title:'Find Median from Data Stream',difficulty:'hard',category:'heaps',patterns:['heap'],cognitiveLoad:'high',recommendedTime:30,
statement:'Design a data structure that supports:\n- addNum(num): add an integer from the data stream\n- findMedian(): return the median of all elements so far\n\nThe median is the middle value in an ordered list. If the list size is even, it is the average of the two middle values.',
examples:[{input:'addNum(1), addNum(2), findMedian(), addNum(3), findMedian()',output:'null, null, 1.5, null, 2.0',explanation:'After [1,2] median is 1.5. After [1,2,3] median is 2.'}],
commonMistakes:['Not balancing the two heaps','Getting confused about max-heap vs min-heap','Not handling the even/odd count correctly'],
discussionPoints:['Two heaps: max-heap for lower half, min-heap for upper half','Balance invariant: sizes differ by at most 1','Why binary search insert is O(n) due to shifting'],
bruteForce:{approach:'Keep sorted array, insert with binary search, return middle.',timeComplexity:'O(n) insert (shifting), O(1) median',spaceComplexity:'O(n)',code:'import bisect\nnums = []\ndef addNum(num): bisect.insort(nums, num)\ndef findMedian():\n  n = len(nums)\n  if n % 2: return nums[n//2]\n  return (nums[n//2-1] + nums[n//2]) / 2'},
optimal:{approach:'Two heaps: max-heap (lower half) and min-heap (upper half). Keep them balanced (size difference <= 1).',timeComplexity:'O(log n) insert, O(1) median',spaceComplexity:'O(n)',code:'import heapq\nclass MedianFinder:\n  def __init__(self):\n    self.lo = []  # max-heap (negate values)\n    self.hi = []  # min-heap\n  \n  def addNum(self, num):\n    heapq.heappush(self.lo, -num)\n    heapq.heappush(self.hi, -heapq.heappop(self.lo))\n    if len(self.hi) > len(self.lo):\n      heapq.heappush(self.lo, -heapq.heappop(self.hi))\n  \n  def findMedian(self):\n    if len(self.lo) > len(self.hi):\n      return -self.lo[0]\n    return (-self.lo[0] + self.hi[0]) / 2'},
hints:['You need quick access to the middle elements.','What if you split the data into a lower half and upper half?','A max-heap for the lower half gives you the largest small element.','A min-heap for the upper half gives you the smallest large element.','Keep heaps balanced: sizes differ by at most 1. Median is from the tops.'],
edgeCases:['Single element','Two elements','All same elements','Alternating large and small numbers'],
followUps:['What if you need to remove elements too?','Sliding window median?','What if 99% of numbers are between 0 and 100?'],
solutionExplanation:'Maintain two heaps splitting the data in half. The max-heap stores the lower half (top = largest of lower). The min-heap stores the upper half (top = smallest of upper). Keep them balanced. The median is either the max-heap top (odd count) or the average of both tops (even count).',
patternQuiz:{summary:'Design a data structure that efficiently adds numbers and finds the median at any point.',correct:'heap',distractors:['binary-search','sorting','two-pointers']}},

{id:24,title:'Merge Two Sorted Lists',difficulty:'easy',category:'linked-lists',patterns:['two-pointers'],cognitiveLoad:'low',recommendedTime:15,
statement:'You are given the heads of two sorted linked lists list1 and list2. Merge the two lists into one sorted list by splicing together the nodes. Return the head of the merged list.',
examples:[{input:'list1 = [1,2,4], list2 = [1,3,4]',output:'[1,1,2,3,4,4]',explanation:''}],
commonMistakes:['Not handling when one list is exhausted','Losing track of the head of the merged list','Not using a dummy/sentinel node'],
discussionPoints:['Dummy node simplifies edge cases','Iterative vs recursive','This is the merge step of merge sort'],
bruteForce:{approach:'Combine both into array, sort, rebuild list.',timeComplexity:'O((n+m) log(n+m))',spaceComplexity:'O(n+m)',code:'Collect all values, sort, build new list'},
optimal:{approach:'Two pointers: compare heads, attach smaller one, advance that pointer. Use a dummy node.',timeComplexity:'O(n+m)',spaceComplexity:'O(1)',code:'def mergeTwoLists(l1, l2):\n  dummy = ListNode(0)\n  curr = dummy\n  while l1 and l2:\n    if l1.val <= l2.val:\n      curr.next = l1\n      l1 = l1.next\n    else:\n      curr.next = l2\n      l2 = l2.next\n    curr = curr.next\n  curr.next = l1 or l2\n  return dummy.next'},
hints:['Compare the heads of both lists.','Attach the smaller head to your result and advance that list.','Use a dummy node so you don\'t need special logic for the first node.','When one list is exhausted, attach the rest of the other.','Return dummy.next as the head.'],
edgeCases:['One or both lists empty','Lists of different lengths','All elements in one list smaller than the other'],
followUps:['Merge k sorted lists','Merge sort a linked list','Intersection of two sorted lists'],
solutionExplanation:'Classic merge operation. A dummy node avoids edge cases for the first element. Compare heads of both lists, attach the smaller one, advance. When one list runs out, attach the remainder of the other.',
patternQuiz:{summary:'Merge two sorted linked lists into a single sorted linked list.',correct:'two-pointers',distractors:['merge-sort','binary-search','heap']}},

{id:25,title:'Valid Anagram',difficulty:'easy',category:'strings',patterns:['hash-map'],cognitiveLoad:'low',recommendedTime:10,
statement:'Given two strings s and t, return true if t is an anagram of s, and false otherwise.\n\nAn anagram uses all original letters exactly once, rearranged.',
examples:[{input:'s = "anagram", t = "nagaram"',output:'true',explanation:''},{input:'s = "rat", t = "car"',output:'false',explanation:''}],
commonMistakes:['Not checking if lengths are equal first','Only checking one direction of character counts','Using sorting when counting is more efficient'],
discussionPoints:['Sort and compare vs character counting','Unicode considerations','Early termination optimizations'],
bruteForce:{approach:'Sort both strings and compare.',timeComplexity:'O(n log n)',spaceComplexity:'O(n)',code:'return sorted(s) == sorted(t)'},
optimal:{approach:'Count character frequencies with a hash map or array. Both strings should have identical counts.',timeComplexity:'O(n)',spaceComplexity:'O(1) — at most 26 letters',code:'from collections import Counter\ndef isAnagram(s, t):\n  return Counter(s) == Counter(t)\n\n# Or manually:\nif len(s) != len(t): return False\ncount = [0] * 26\nfor c in s: count[ord(c)-ord("a")] += 1\nfor c in t: count[ord(c)-ord("a")] -= 1\nreturn all(c == 0 for c in count)'},
hints:['Anagrams have the same characters in the same quantities.','Quick check: are the lengths equal?','Count character frequencies in both strings.','An array of size 26 works for lowercase English letters.','Increment for s, decrement for t, check all zeros.'],
edgeCases:['Empty strings','Single character','Same string','Different lengths'],
followUps:['What if inputs contain Unicode?','What about finding all anagrams in a string (sliding window)?','Group anagrams in a list of strings?'],
solutionExplanation:'Anagrams have identical character frequency distributions. Counting characters with a fixed-size array (26 for lowercase) is O(n) time and O(1) space. Sorting works but is O(n log n).',
patternQuiz:{summary:'Determine if two strings are anagrams of each other.',correct:'hash-map',distractors:['two-pointers','sorting','sliding-window']}}
],

// ===== COMPLEXITY DRILLS =====
complexityDrills:[
{question:'What is the time complexity?',code:'for (let i = 0; i < n; i++) {\n  console.log(i);\n}',correct:'O(n)',options:['O(1)','O(n)','O(n²)','O(log n)'],explanation:'Single loop iterating n times = O(n).',canImprove:false,improvement:''},
{question:'What is the time complexity?',code:'for (let i = 0; i < n; i++) {\n  for (let j = 0; j < n; j++) {\n    console.log(i, j);\n  }\n}',correct:'O(n²)',options:['O(n)','O(n²)','O(n log n)','O(2n)'],explanation:'Two nested loops each running n times = n × n = O(n²).',canImprove:true,improvement:'If the inner work doesn\'t depend on both indices, check if you can use a hash map or sorting to eliminate the inner loop.'},
{question:'What is the time complexity?',code:'let lo = 0, hi = n - 1;\nwhile (lo <= hi) {\n  let mid = Math.floor((lo + hi) / 2);\n  if (arr[mid] === target) return mid;\n  else if (arr[mid] < target) lo = mid + 1;\n  else hi = mid - 1;\n}',correct:'O(log n)',options:['O(n)','O(log n)','O(n log n)','O(1)'],explanation:'Binary search halves the search space each step = O(log n).',canImprove:false,improvement:''},
{question:'What is the time complexity?',code:'function fib(n) {\n  if (n <= 1) return n;\n  return fib(n - 1) + fib(n - 2);\n}',correct:'O(2^n)',options:['O(n)','O(n²)','O(2^n)','O(n log n)'],explanation:'Each call branches into two, creating an exponential call tree. With memoization this becomes O(n).',canImprove:true,improvement:'Add memoization to reduce to O(n) time, O(n) space.'},
{question:'What is the time complexity?',code:'arr.sort();\nfor (let i = 0; i < n; i++) {\n  // binary search in arr\n  binarySearch(arr, target[i]);\n}',correct:'O(n log n)',options:['O(n)','O(n log n)','O(n²)','O(log n)'],explanation:'Sort is O(n log n). Loop with binary search is O(n log n). Total: O(n log n).',canImprove:false,improvement:''},
{question:'What is the space complexity?',code:'function dfs(node) {\n  if (!node) return 0;\n  return 1 + Math.max(dfs(node.left), dfs(node.right));\n}',correct:'O(h)',options:['O(1)','O(n)','O(h)','O(log n)'],explanation:'Recursion depth equals tree height h. In worst case (skewed tree) h = n; for balanced tree h = log n.',canImprove:false,improvement:''},
{question:'What is the time complexity?',code:'let result = "";\nfor (let i = 0; i < n; i++) {\n  result += chars[i]; // string concat\n}',correct:'O(n²)',options:['O(n)','O(n²)','O(n log n)','O(1)'],explanation:'String concatenation creates a new string each time. Length grows 1,2,3,...n = O(n²) total work. Use an array and join instead.',canImprove:true,improvement:'Use an array to collect characters and join at the end for O(n).'},
{question:'What is the time complexity?',code:'const set = new Set();\nfor (let i = 0; i < n; i++) {\n  set.add(arr[i]);\n}\nfor (let i = 0; i < n; i++) {\n  if (set.has(target - arr[i])) return true;\n}',correct:'O(n)',options:['O(n)','O(n²)','O(n log n)','O(1)'],explanation:'Two sequential loops over n elements with O(1) set operations = O(n) + O(n) = O(n).',canImprove:false,improvement:''},
{question:'What is the time complexity of mergesort?',code:'function mergeSort(arr) {\n  if (arr.length <= 1) return arr;\n  const mid = Math.floor(arr.length / 2);\n  const left = mergeSort(arr.slice(0, mid));\n  const right = mergeSort(arr.slice(mid));\n  return merge(left, right);\n}',correct:'O(n log n)',options:['O(n)','O(n log n)','O(n²)','O(log n)'],explanation:'log n levels of recursion, each doing O(n) merge work = O(n log n). This is optimal for comparison-based sorting.',canImprove:false,improvement:''},
{question:'What is the amortized time for dynamic array push?',code:'// Dynamic array (e.g. ArrayList, vector)\n// When full, doubles capacity and copies\narr.push(element);',correct:'O(1) amortized',options:['O(1) amortized','O(n)','O(log n)','O(1) worst case'],explanation:'Most pushes are O(1). Occasionally doubles and copies (O(n)), but spread across n pushes the average is O(1).',canImprove:false,improvement:''},
{question:'What is the time complexity?',code:'for (let i = 0; i < n; i++) {\n  for (let j = i + 1; j < n; j++) {\n    for (let k = j + 1; k < n; k++) {\n      // constant work\n    }\n  }\n}',correct:'O(n³)',options:['O(n²)','O(n³)','O(n² log n)','O(2^n)'],explanation:'Three nested loops, each up to n. The total is C(n,3) = O(n³).',canImprove:true,improvement:'Depends on the problem. For 3Sum, sorting + two pointers reduces to O(n²).'},
{question:'What is the time complexity?',code:'for (let i = 1; i < n; i *= 2) {\n  // constant work\n}',correct:'O(log n)',options:['O(n)','O(log n)','O(n log n)','O(sqrt(n))'],explanation:'i doubles each iteration: 1, 2, 4, 8, ..., n. Takes log₂(n) steps.',canImprove:false,improvement:''}
],

// ===== FLASHCARDS =====
flashcards:[
{front:'Stack vs Queue: what\'s the key difference?',back:'Stack is LIFO (Last In, First Out) — like a stack of plates. Queue is FIFO (First In, First Out) — like a line of people. Stack: push/pop. Queue: enqueue/dequeue.'},
{front:'BFS vs DFS: when to use which?',back:'BFS (queue): shortest path in unweighted graphs, level-order traversal. DFS (stack/recursion): detecting cycles, topological sort, exhaustive search, path finding. BFS uses more memory (stores whole level); DFS uses stack space (depth).'},
{front:'Array vs Linked List trade-offs',back:'Array: O(1) random access, O(n) insert/delete, contiguous memory (cache-friendly). Linked List: O(n) access, O(1) insert/delete at known position, dynamic size, extra memory for pointers.'},
{front:'What defines a valid Binary Search Tree?',back:'For every node: all values in left subtree < node < all values in right subtree. Both subtrees must also be valid BSTs. Inorder traversal produces a sorted sequence.'},
{front:'Heap properties',back:'Min-heap: parent <= children (root is minimum). Max-heap: parent >= children (root is maximum). Complete binary tree. Insert and extract: O(log n). Peek: O(1). Used for priority queues, top-k problems, median finding.'},
{front:'Hash table collision resolution',back:'Chaining: each bucket stores a linked list of entries. Open addressing: probe for next empty slot (linear probing, quadratic probing, double hashing). Average O(1) operations; worst case O(n) with many collisions.'},
{front:'Dynamic Programming: top-down vs bottom-up',back:'Top-down (memoization): recursive + cache, natural to write, solves only needed subproblems. Bottom-up (tabulation): iterative, fills table from base cases, often more space-efficient, no recursion overhead.'},
{front:'Recursion base case: why is it critical?',back:'The base case stops the recursion. Without it: infinite recursion → stack overflow. Every recursive function needs at least one base case that returns without recursing. Common: empty input, size 0 or 1, null node.'},
{front:'Backtracking template',back:'1. Choose: make a decision (add element, pick path). 2. Explore: recurse with the choice. 3. Unchoose: undo the decision (backtrack). Key: restore state after exploring each branch. Used for: subsets, permutations, N-queens, word search.'},
{front:'Greedy vs Dynamic Programming',back:'Greedy: make locally optimal choice at each step, hope it\'s globally optimal. Only works when greedy choice property holds. DP: consider all subproblems, combine optimally. Always gives optimal result but may be slower. If greedy works, it\'s simpler and faster.'},
{front:'Common complexity classes (slowest to fastest)',back:'O(n!) → O(2^n) → O(n³) → O(n²) → O(n log n) → O(n) → O(√n) → O(log n) → O(1). Most interview solutions should be O(n log n) or better. O(n²) is often acceptable for n ≤ 10⁴.'},
{front:'Binary search: what are the preconditions?',back:'The search space must be monotonic (sorted or has a clear decision boundary). Key decisions: inclusive vs exclusive bounds, how to update lo/hi, when to stop. Common bug: integer overflow in mid = (lo+hi)/2; use lo + (hi-lo)/2 instead.'},
{front:'Sliding window: when to use it?',back:'Use when: finding subarrays/substrings with a property (max, min, sum, unique chars), and expanding/shrinking from one end is valid. Fixed window: both pointers move together. Variable window: right expands, left shrinks when condition violated.'},
{front:'Two pointers: common patterns',back:'1. Start/end convergence: sorted array (two sum, container with most water). 2. Slow/fast: linked list cycle detection, finding middle. 3. Same direction: merging sorted arrays, removing duplicates. Requires sorted or structured data.'},
{front:'Tree traversal types',back:'Inorder (Left, Node, Right): BST gives sorted order. Preorder (Node, Left, Right): copy tree, serialize. Postorder (Left, Right, Node): delete tree, evaluate expressions. Level-order (BFS): shortest path, level-by-level processing.'},
{front:'Graph representations',back:'Adjacency list: space O(V+E), good for sparse graphs, fast iteration over neighbors. Adjacency matrix: space O(V²), good for dense graphs, O(1) edge lookup. Edge list: space O(E), good for algorithms like Kruskal\'s.'},
{front:'Stable vs unstable sorting',back:'Stable sort preserves relative order of equal elements. Stable: merge sort, insertion sort, counting sort. Unstable: quicksort, heapsort, selection sort. Matters when sorting by multiple keys or preserving original order of ties.'},
{front:'Topological sort: what is it for?',back:'Orders vertices in a DAG (directed acyclic graph) such that for every edge u→v, u comes before v. Use cases: task scheduling, build systems, course prerequisites. Algorithms: Kahn\'s (BFS with in-degree) or DFS with post-order. Only works on DAGs.'},
{front:'Trie: when to use it?',back:'When you need prefix-based operations: autocomplete, spell checking, prefix matching, word search. O(m) per operation where m = word length. More space than hash set but supports prefix queries that hash sets cannot.'},
{front:'Union-Find (Disjoint Set): what is it?',back:'Data structure for tracking connected components. Operations: find(x) — which set does x belong to? union(x,y) — merge sets containing x and y. With path compression + union by rank: nearly O(1) amortized. Used for: connected components, Kruskal\'s MST, cycle detection in undirected graphs.'}
],

}}
</script>
@endpush

@push('styles')
<style>[x-cloak]{display:none!important}</style>
@endpush
