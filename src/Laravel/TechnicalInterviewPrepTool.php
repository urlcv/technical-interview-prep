<?php

declare(strict_types=1);

namespace URLCV\TechnicalInterviewPrep\Laravel;

use App\Tools\Contracts\ToolInterface;

class TechnicalInterviewPrepTool implements ToolInterface
{
    public function slug(): string
    {
        return 'technical-interview-prep';
    }

    public function name(): string
    {
        return 'Technical Interview Prep';
    }

    public function summary(): string
    {
        return 'Master algorithms, data structures, and complexity analysis with guided practice, pattern drills, and ADHD-friendly spaced repetition.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## Technical Interview Prep

A complete, browser-based training system for technical coding interviews. Designed for ADHD brains — every feature prioritises low friction, visible progress, and immediate feedback.

### Core features
- **25+ algorithm problems** across arrays, strings, trees, graphs, DP, and more — each with 5-level progressive hints, brute-force and optimal solutions, complexity analysis, and interviewer follow-ups
- **Guided problem-solving flow** — an 11-step structured interview simulation from restating the problem through coding to reflection
- **Pattern recognition drills** — identify which algorithmic pattern applies to a problem summary
- **Complexity drills** — analyse code snippets for time and space complexity
- **Mock interview mode** — timed, realistic simulations with follow-up questions (20-minute ADHD-friendly default)
- **Flashcards & rapid revision** — spaced-repetition flashcards covering core concepts
- **Focus Map dashboard** — see your strongest and weakest areas with a single clear next action
- **Spaced repetition (SM-2)** — weak problems resurface automatically at optimal intervals

### ADHD-friendly design
- Energy-based session modes (10 / 30 / 60 min)
- Warm-up rounds to build momentum before hard problems
- Sentence starters to prevent blank-page paralysis
- Stuck timer with gentle nudges
- Quick-exit bookmarking with instant resume
- Streak system with a very low minimum bar
- Positive framing — no guilt, no failure language, just growth areas
- Keyboard shortcuts for every action

### How it works
Everything runs entirely in your browser. No server calls, no accounts needed. Progress is saved to localStorage and persists across sessions.
MD;
    }

    public function categories(): array
    {
        return ['interviews', 'productivity'];
    }

    public function tags(): array
    {
        return ['interview', 'algorithms', 'coding', 'data-structures', 'complexity', 'leetcode', 'practice', 'adhd'];
    }

    public function inputSchema(): array
    {
        return [];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'technical-interview-prep::technical-interview-prep';
    }

    public function rateLimitPerMinute(): int
    {
        return 30;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 50;
    }
}
