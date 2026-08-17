<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWeakWord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeakWordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $weakWords = UserWeakWord::with('word')
            ->where('user_id', $request->user()->id)
            ->active()
            ->orderByDesc('wrong_count')
            ->get();

        return response()->json(['data' => $weakWords->map(fn (UserWeakWord $w) => [
            'id' => (string) $w->id,
            'word' => $w->word->word,
            'meaningId' => $w->word->meaning_id,
            'meaningEn' => $w->word->meaning_en,
            'level' => $w->word->level,
            'wrongCount' => $w->wrong_count,
            'correctStreak' => $w->correct_streak,
            'lastWrongAt' => $w->last_wrong_at?->toIso8601String(),
        ])->values()]);
    }
}
