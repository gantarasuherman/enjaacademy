<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GrammarLevelResource;
use App\Http\Resources\GrammarPatternResource;
use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use App\Models\GrammarPattern;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GrammarController extends Controller
{
    /** Every level with its category tree — the whole nav structure in one call. */
    public function levels(): AnonymousResourceCollection
    {
        return GrammarLevelResource::collection(
            GrammarLevel::query()
                ->where('is_active', true)
                ->with([
                    'categories' => fn ($q) => $q->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order'),
                    'categories.patterns' => fn ($q) => $q->published(),
                    'categories.children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                    'categories.children.patterns' => fn ($q) => $q->published(),
                ])
                ->orderBy('sort_order')
                ->get(),
        );
    }

    /** Published patterns in one category (list view — no items, matches the lesson-list pattern elsewhere). */
    public function patterns(Request $request, GrammarCategory $grammarCategory): AnonymousResourceCollection
    {
        $patterns = GrammarPattern::query()
            ->where('grammar_category_id', $grammarCategory->id)
            ->published()
            ->with('category.level')
            ->orderBy('sort_order')
            ->get();

        return GrammarPatternResource::collection($patterns);
    }

    public function show(GrammarPattern $grammarPattern): GrammarPatternResource
    {
        $this->authorize('view', $grammarPattern);

        return new GrammarPatternResource($grammarPattern->load(['category.level', 'items']));
    }
}
