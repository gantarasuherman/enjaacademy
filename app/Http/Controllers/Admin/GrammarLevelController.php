<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrammarLevelRequest;
use App\Models\GrammarLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GrammarLevelController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', GrammarLevel::class);

        return view('admin.grammar.levels.index', [
            'levels' => GrammarLevel::withCount('categories')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', GrammarLevel::class);

        return view('admin.grammar.levels.form', ['level' => new GrammarLevel(['is_active' => true, 'color' => 'indigo'])]);
    }

    public function store(GrammarLevelRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        $level = GrammarLevel::create($data);

        return redirect()->route('admin.grammar.levels.index')
            ->with('success', __('Level ":name" was created.', ['name' => $level->name]));
    }

    public function edit(GrammarLevel $grammarLevel): View
    {
        $this->authorize('update', $grammarLevel);

        return view('admin.grammar.levels.form', ['level' => $grammarLevel]);
    }

    public function update(GrammarLevelRequest $request, GrammarLevel $grammarLevel): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: $grammarLevel->slug;

        $grammarLevel->update($data);

        return redirect()->route('admin.grammar.levels.index')
            ->with('success', __('Level ":name" was updated.', ['name' => $grammarLevel->name]));
    }

    public function destroy(GrammarLevel $grammarLevel): RedirectResponse
    {
        $this->authorize('delete', $grammarLevel);

        if ($grammarLevel->categories()->exists()) {
            return back()->with('error', __('Remove or move this level\'s categories before deleting it.'));
        }

        $grammarLevel->delete();

        return back()->with('success', __('Level ":name" was deleted.', ['name' => $grammarLevel->name]));
    }
}
