<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\LearningModule;
use App\Models\User;

interface EnrollmentRepositoryInterface extends RepositoryInterface
{
    public function isEnrolled(User $user, LearningModule $module): bool;

    /** Enrolls the user if not already enrolled, otherwise unenrolls them. Returns the new state. */
    public function toggle(User $user, LearningModule $module): bool;
}
