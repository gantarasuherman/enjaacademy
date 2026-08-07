<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LearningModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LearningModulePolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'modules';
    }

    /**
     * Reading a module's content is governed by the module's *own* permission
     * prefix, which is why a brand new module needs no new policy class.
     */
    public function view(User $user, Model $model): bool
    {
        return $model instanceof LearningModule
            && ($user->can($model->permission('view')) || $user->can('modules.view'));
    }

    public function study(User $user, LearningModule $module): bool
    {
        return $module->is_active && $user->can($module->permission('view'));
    }
}
