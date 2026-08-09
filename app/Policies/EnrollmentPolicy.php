<?php

declare(strict_types=1);

namespace App\Policies;

class EnrollmentPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'enrollments';
    }
}
