<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\System\ImportExportService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserRepositoryInterface $users,
        private readonly AuditLogRepositoryInterface $audits,
        private readonly ImportExportService $io,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => $this->users->paginate(
                $request->only(['search', 'role', 'is_active', 'verified', 'trashed', 'sort', 'direction']),
                $this->perPage(),
            ),
            'roles' => Role::orderBy('name')->pluck('name', 'name'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.form', [
            'user' => new User(['is_active' => true, 'locale' => config('app.locale')]),
            'roles' => Role::orderBy('name')->get(),
            'assigned' => [],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = $this->service->create(UserData::fromRequest($request));

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('User ":name" was created.', ['name' => $user->name]));
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('admin.users.show', [
            'user' => $user->load(['roles', 'stat', 'achievements']),
            'activity' => $this->audits->paginate(['user_id' => $user->id], 15),
            'attempts' => $user->quizAttempts()->with('quiz:id,title')->latest()->limit(10)->get(),
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.form', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'assigned' => $user->getRoleNames()->all(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->service->update($user, UserData::fromRequest($request));

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('User ":name" was updated.', ['name' => $user->name]));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->service->delete($user);

        return back()->with('success', __('User ":name" was deleted.', ['name' => $user->name]));
    }

    public function toggle(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user = $this->service->toggleActive($user);

        return back()->with('success', $user->is_active
            ? __('User ":name" is now active.', ['name' => $user->name])
            : __('User ":name" is now inactive.', ['name' => $user->name]));
    }

    public function assignRoles(Request $request, User $user): RedirectResponse
    {
        $this->authorize('assignRoles', $user);

        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $this->service->assignRoles($user, $validated['roles'] ?? []);

        return back()->with('success', __('Roles updated for ":name".', ['name' => $user->name]));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('export', User::class);

        $users = $this->users
            ->paginate($request->only(['search', 'role', 'is_active']), 10000)
            ->getCollection();

        return $this->io->streamCsv(
            'users-'.now()->format('Ymd-His').'.csv',
            ['id', 'name', 'email', 'roles', 'active', 'verified_at', 'created_at'],
            $users->map(fn (User $user) => [
                $user->id,
                $user->name,
                $user->email,
                $user->getRoleNames()->implode('|'),
                $user->is_active ? 'yes' : 'no',
                $user->email_verified_at?->toDateTimeString(),
                $user->created_at?->toDateTimeString(),
            ]),
        );
    }
}
