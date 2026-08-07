<?php

declare(strict_types=1);

namespace App\Policies;

class AchievementPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'achievements';
    }
}
