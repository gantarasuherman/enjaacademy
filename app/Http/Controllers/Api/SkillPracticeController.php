<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Services\Gamification\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Speaking and Writing practice have no dedicated content tables of their
 * own yet — both piggyback on the generic Lesson/LessonItem API (see
 * frontend/src/services/api/content.service.ts). This controller is just
 * the missing XP half of that: previously both pages awarded XP by
 * mutating the client-side Zustand store directly, which never reached
 * `ProgressService`, so it was lost on reload or on another device.
 */
class SkillPracticeController extends Controller
{
    public function __construct(private readonly ProgressService $progress) {}

    /** Pronunciation scoring itself stays client-side (Web Speech API has no server equivalent) — this just persists the XP for it. */
    public function completeSpeaking(Request $request, LessonItem $item): JsonResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $xp = match (true) {
            $validated['score'] >= 90 => (int) config('admin.gamification.xp.speaking_excellent'),
            $validated['score'] >= 70 => (int) config('admin.gamification.xp.speaking_pass'),
            default => 0,
        };

        $earned = $this->progress->completeSkillPractice($request->user(), $item, $xp, __('Speaking practice'));

        return response()->json(['data' => ['earnedXp' => $earned]]);
    }

    /** No grading/rubric backend yet — completion itself (a real attempt of reasonable length) is what's rewarded. */
    public function submitWriting(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'min:20'],
        ]);

        $xp = (int) config('admin.gamification.xp.writing_submitted');

        $earned = $this->progress->completeSkillPractice($request->user(), $lesson, $xp, __('Writing submission'));

        return response()->json(['data' => ['earnedXp' => $earned]]);
    }
}
