<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrammarPatternRequest;
use App\Models\GrammarCategory;
use App\Models\GrammarPattern;
use App\Models\GrammarPatternExample;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GrammarPatternController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GrammarPattern::class);

        $patterns = GrammarPattern::query()
            ->with(['category.level'])
            ->when($request->filled('category'), fn ($q) => $q->where('grammar_category_id', $request->integer('category')))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('id')
            ->paginate($this->perPage())
            ->withQueryString();

        return view('admin.grammar.patterns.index', [
            'patterns' => $patterns,
            'categories' => GrammarCategory::with('level')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', GrammarPattern::class);

        return view('admin.grammar.patterns.form', [
            'pattern' => new GrammarPattern(['is_published' => false]),
            'items' => collect(),
            'categories' => GrammarCategory::with('level')->orderBy('name')->get(),
        ]);
    }

    public function store(GrammarPatternRequest $request): RedirectResponse
    {
        $pattern = DB::transaction(function () use ($request) {
            $data = $request->safe()->except('items');
            $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['title']);
            $data['created_by'] = $request->user()->id;
            if (! empty($data['is_published'])) {
                $data['published_at'] = now();
            }

            $pattern = GrammarPattern::create($data);
            $this->syncItems($pattern, $request->input('items', []));

            return $pattern;
        });

        return redirect()->route('admin.grammar.patterns.edit', $pattern)
            ->with('success', __('Pattern ":title" was created.', ['title' => $pattern->title]));
    }

    public function edit(GrammarPattern $grammarPattern): View
    {
        $this->authorize('update', $grammarPattern);

        return view('admin.grammar.patterns.form', [
            'pattern' => $grammarPattern,
            'items' => $grammarPattern->items,
            'categories' => GrammarCategory::with('level')->orderBy('name')->get(),
        ]);
    }

    public function update(GrammarPatternRequest $request, GrammarPattern $grammarPattern): RedirectResponse
    {
        DB::transaction(function () use ($request, $grammarPattern) {
            $data = $request->safe()->except('items');
            $data['slug'] = ($data['slug'] ?? '') ?: $grammarPattern->slug;
            if (! empty($data['is_published']) && ! $grammarPattern->is_published) {
                $data['published_at'] = now();
            }

            $grammarPattern->update($data);
            $this->syncItems($grammarPattern, $request->input('items', []));
        });

        return back()->with('success', __('Pattern ":title" was updated.', ['title' => $grammarPattern->title]));
    }

    public function destroy(GrammarPattern $grammarPattern): RedirectResponse
    {
        $this->authorize('delete', $grammarPattern);

        $grammarPattern->delete();

        return redirect()->route('admin.grammar.patterns.index')
            ->with('success', __('Pattern ":title" was deleted.', ['title' => $grammarPattern->title]));
    }

    /** Wholesale replace, mirroring LearningService::syncItems() — simple and predictable for a form that posts the whole list every save. */
    private function syncItems(GrammarPattern $pattern, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $keptIds = [];

        foreach (array_values($rows) as $index => $row) {
            if (blank($row['sentence'] ?? null)) {
                continue;
            }

            // A blank `items[x][id]` from the form is an empty string, not an
            // absent key — `?:` (not `??`) is what actually treats it as "new".
            $item = (! empty($row['id']))
                ? $pattern->items()->find($row['id']) ?? new GrammarPatternExample(['grammar_pattern_id' => $pattern->id])
                : new GrammarPatternExample(['grammar_pattern_id' => $pattern->id]);

            $item->fill([
                'type' => $row['type'] ?? 'example',
                'sentence' => $row['sentence'],
                'romaji' => $row['romaji'] ?? null,
                'translation' => $row['translation'] ?? null,
                'correction' => $row['correction'] ?? null,
                'correction_romaji' => $row['correction_romaji'] ?? null,
                'note' => $row['note'] ?? null,
                'sort_order' => $index,
            ])->save();

            $keptIds[] = $item->id;
        }

        $pattern->items()->whereNotIn('id', $keptIds ?: [0])->delete();
    }
}
