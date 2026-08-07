<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DataTransferObjects\UserData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Audit\AuditLogger;
use App\Services\Menu\MenuCache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly MenuCache $menuCache,
        private readonly AuditLogger $audit,
    ) {}

    public function create(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();

            if ($data->markVerified) {
                $attributes['email_verified_at'] = now();
            }

            if ($data->avatar) {
                $attributes['avatar'] = $this->storeAvatar($data->avatar);
            }

            /** @var User $user */
            $user = $this->users->create($attributes);

            $user->syncRoles($data->roles);
            $user->stat()->create();

            $this->menuCache->flush();

            return $user;
        });
    }

    public function update(User $user, UserData $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $attributes = $data->toArray();

            if ($data->avatar) {
                $this->deleteAvatar($user);
                $attributes['avatar'] = $this->storeAvatar($data->avatar);
            }

            if ($data->markVerified && $user->email_verified_at === null) {
                $attributes['email_verified_at'] = now();
            }

            // Changing the email invalidates the previous verification.
            if ($user->email !== $data->email) {
                $attributes['email_verified_at'] = $data->markVerified ? now() : null;
            }

            $this->users->update($user, $attributes);

            $this->guardLastSuperAdmin($user, $data->roles);
            $user->syncRoles($data->roles);

            $this->menuCache->flush();

            return $user->refresh();
        });
    }

    public function delete(User $user): void
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => __('You cannot delete your own account from here.'),
            ]);
        }

        $this->guardLastSuperAdmin($user, []);

        $this->users->delete($user);
        $this->menuCache->flush();
    }

    public function toggleActive(User $user): User
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => __('You cannot deactivate your own account.'),
            ]);
        }

        $this->users->update($user, ['is_active' => ! $user->is_active]);

        $this->audit->event(
            $user->is_active ? 'activated' : 'deactivated',
            __('User :name was :state.', ['name' => $user->name, 'state' => $user->is_active ? 'activated' : 'deactivated']),
            $user,
        );

        return $user->refresh();
    }

    public function assignRoles(User $user, array $roles): User
    {
        $this->guardLastSuperAdmin($user, $roles);

        $user->syncRoles($roles);

        $this->audit->event('roles_synced', __('Roles set to: :roles', ['roles' => implode(', ', $roles) ?: '—']), $user);
        $this->menuCache->flush();

        return $user->refresh();
    }

    public function recordLogin(User $user): void
    {
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'last_activity_at' => now(),
        ])->saveQuietly();

        $this->audit->event('login', __('Signed in.'), $user);
    }

    private function storeAvatar(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    private function deleteAvatar(User $user): void
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
    }

    /**
     * Locking everyone out of the super role would leave the panel
     * unadministrable, so the last holder can never lose it.
     */
    private function guardLastSuperAdmin(User $user, array $newRoles): void
    {
        $superRole = config('admin.super_role');

        if (! $user->hasRole($superRole) || in_array($superRole, $newRoles, true)) {
            return;
        }

        $remaining = User::role($superRole)->whereKeyNot($user->getKey())->count();

        if ($remaining === 0) {
            throw ValidationException::withMessages([
                'roles' => __('At least one :role must remain in the system.', ['role' => $superRole]),
            ]);
        }
    }
}
