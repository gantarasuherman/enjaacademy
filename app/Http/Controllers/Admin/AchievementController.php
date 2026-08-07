<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\AchievementData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AchievementRequest;
use App\Models\Achievement;
use App\Repositories\Contracts\AchievementRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __construct(private readonly AchievementRepositoryInterface $achievements) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Achievement::class);

        return view('admin.achievements.index', [
            'achievements' => $this->achievements->paginate(
                $request->only(['search', 'criteria_type', 'is_active', 'sort', 'direction']),
                $this->perPage(),
            ),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Achievement::class);

        return view('admin.achievements.form', [
            'achievement' => new Achievement(['is_active' => true, 'badge_color' => 'amber', 'criteria_type' => 'xp_total']),
        ]);
    }

    public function store(AchievementRequest $request): RedirectResponse
    {
        $achievement = $this->achievements->create(AchievementData::fromRequest($request)->toArray());

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', __('Achievement ":name" was created.', ['name' => $achievement->name]));
    }

    public function edit(Achievement $achievement): View
    {
        $this->authorize('update', $achievement);

        return view('admin.achievements.form', ['achievement' => $achievement]);
    }

    public function update(AchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        $this->achievements->update($achievement, AchievementData::fromRequest($request)->toArray());

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', __('Achievement ":name" was updated.', ['name' => $achievement->name]));
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorize('delete', $achievement);

        $this->achievements->delete($achievement);

        return back()->with('success', __('Achievement ":name" was deleted.', ['name' => $achievement->name]));
    }
}
