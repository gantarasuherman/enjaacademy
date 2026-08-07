<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class MenuPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'menus';
    }

    public function reorder(User $user): bool
    {
        return $user->can('menus.update');
    }
}
