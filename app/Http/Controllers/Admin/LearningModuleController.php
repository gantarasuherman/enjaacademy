<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\LearningModuleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LearningModuleRequest;
use App\Models\LearningModule;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Services\Learning\LearningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningModuleController extends Controller
{
    public function __construct(
        private readonly LearningService $service,
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LanguageRepositoryInterface $languages,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', LearningModule::class);

        return view('admin.modules.index', [
            'modules' => $this->modules->paginate(
                $request->only(['search', 'language', 'content_type', 'is_active', 'sort', 'direction']),
                $this->perPage(),
            ),
            'languages' => $this->languages->active(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', LearningModule::class);

        return view('admin.modules.form', [
            'module' => new LearningModule(['is_active' => true, 'color' => 'indigo', 'content_type' => 'vocabulary']),
            'languages' => $this->languages->active(),
        ]);
    }

    public function store(LearningModuleRequest $request): RedirectResponse
    {
        $module = $this->service->createModule(LearningModuleData::fromRequest($request));

        return redirect()
            ->route('admin.modules.index')
            ->with('success', __('Module ":name" was created. Permissions: :prefix.view / .create / .update / .delete', [
                'name' => $module->name,
                'prefix' => $module->permission_prefix,
            ]));
    }

    public function show(LearningModule $module): View
    {
        $this->authorize('view', $module);

        return view('admin.modules.show', [
            'module' => $module->load('language'),
            'lessons' => $module->lessons()->withCount('items')->orderBy('sort_order')->get(),
            'quizzes' => $module->quizzes()->withCount('questions')->get(),
        ]);
    }

    public function edit(LearningModule $module): View
    {
        $this->authorize('update', $module);

        return view('admin.modules.form', [
            'module' => $module,
            'languages' => $this->languages->active(),
        ]);
    }

    public function update(LearningModuleRequest $request, LearningModule $module): RedirectResponse
    {
        $this->service->updateModule($module, LearningModuleData::fromRequest($request));

        return redirect()
            ->route('admin.modules.index')
            ->with('success', __('Module ":name" was updated.', ['name' => $module->name]));
    }

    public function destroy(LearningModule $module): RedirectResponse
    {
        $this->authorize('delete', $module);

        $this->service->deleteModule($module);

        return back()->with('success', __('Module ":name" was deleted.', ['name' => $module->name]));
    }
}
