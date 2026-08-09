<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrammarCategoryRequest;
use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GrammarCategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', GrammarCategory::class);

        return view('admin.grammar.categories.index', [
            'levels' => GrammarLevel::with([
                'categories' => fn ($q) => $q->whereNull('parent_id')->orderBy('sort_order'),
                'categories.children',
            ])
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', GrammarCategory::class);

        return view('admin.grammar.categories.form', [
            'category' => new GrammarCategory(['is_active' => true]),
            'levels' => GrammarLevel::orderBy('sort_order')->get(),
            'parents' => GrammarCategory::orderBy('name')->get(),
        ]);
    }

    public function store(GrammarCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        $category = GrammarCategory::create($data);

        return redirect()->route('admin.grammar.categories.index')
            ->with('success', __('Category ":name" was created.', ['name' => $category->name]));
    }

    public function edit(GrammarCategory $grammarCategory): View
    {
        $this->authorize('update', $grammarCategory);

        return view('admin.grammar.categories.form', [
            'category' => $grammarCategory,
            'levels' => GrammarLevel::orderBy('sort_order')->get(),
            'parents' => GrammarCategory::where('id', '!=', $grammarCategory->id)->orderBy('name')->get(),
        ]);
    }

    public function update(GrammarCategoryRequest $request, GrammarCategory $grammarCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: $grammarCategory->slug;

        $grammarCategory->update($data);

        return redirect()->route('admin.grammar.categories.index')
            ->with('success', __('Category ":name" was updated.', ['name' => $grammarCategory->name]));
    }

    public function destroy(GrammarCategory $grammarCategory): RedirectResponse
    {
        $this->authorize('delete', $grammarCategory);

        if ($grammarCategory->children()->exists() || $grammarCategory->patterns()->exists()) {
            return back()->with('error', __('Remove or move this category\'s contents before deleting it.'));
        }

        $grammarCategory->delete();

        return back()->with('success', __('Category ":name" was deleted.', ['name' => $grammarCategory->name]));
    }
}
