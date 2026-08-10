<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GrammarCategoryResource;
use App\Http\Resources\GrammarLevelResource;
use App\Http\Resources\GrammarPatternResource;
use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use App\Models\GrammarPattern;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GrammarController extends Controller
{
    /**
     * Every level (for one language+track tree) with its category tree — the
     * whole nav structure in one call. Defaults to Japanese Grammar (JLPT)
     * for backwards compatibility with callers that don't pass either param.
     */
    public function levels(Request $request): AnonymousResourceCollection
    {
        $language = $request->query('language', 'japanese');
        $track = $request->query('track', 'grammar');

        return GrammarLevelResource::collection(
            GrammarLevel::query()
                ->forTrack($language, $track)
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

    /**
     * A single category's own metadata (name, level, parent, children) —
     * lets the frontend resolve a category by id directly instead of
     * fetching an entire level tree and searching it, which breaks once
     * categories live in more than one (language, track) tree.
     */
    public function showCategory(GrammarCategory $grammarCategory): GrammarCategoryResource
    {
        return new GrammarCategoryResource($grammarCategory->load([
            'level',
            'parent',
            'children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'children.patterns' => fn ($q) => $q->published(),
        ]));
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
