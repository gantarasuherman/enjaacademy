<?php

declare(strict_types=1);

namespace App\Policies;

class PermissionPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'permissions';
    }
}
