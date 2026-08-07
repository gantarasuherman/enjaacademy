<?php

declare(strict_types=1);

namespace App\Policies;

class LessonItemPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'lessons';
    }
}
