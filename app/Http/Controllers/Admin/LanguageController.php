<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\LanguageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Models\Language;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function __construct(private readonly LanguageRepositoryInterface $languages) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Language::class);

        return view('admin.languages.index', [
            'languages' => $this->languages->paginate($request->only(['search', 'is_active', 'sort', 'direction']), $this->perPage()),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Language::class);

        return view('admin.languages.form', ['language' => new Language(['is_active' => true, 'color' => 'indigo'])]);
    }

    public function store(LanguageRequest $request): RedirectResponse
    {
        $language = $this->languages->create(LanguageData::fromRequest($request)->toArray());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('Language ":name" was created.', ['name' => $language->name]));
    }

    public function edit(Language $language): View
    {
        $this->authorize('update', $language);

        return view('admin.languages.form', ['language' => $language]);
    }

    public function update(LanguageRequest $request, Language $language): RedirectResponse
    {
        $this->languages->update($language, LanguageData::fromRequest($request)->toArray());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('Language ":name" was updated.', ['name' => $language->name]));
    }

    public function destroy(Language $language): RedirectResponse
    {
        $this->authorize('delete', $language);

        if ($language->modules()->exists()) {
            return back()->with('error', __('Remove or move this language\'s modules before deleting it.'));
        }

        $this->languages->delete($language);

        return back()->with('success', __('Language ":name" was deleted.', ['name' => $language->name]));
    }
}
