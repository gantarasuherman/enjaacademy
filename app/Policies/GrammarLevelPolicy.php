<?php

declare(strict_types=1);

namespace App\Policies;

class GrammarLevelPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'grammar';
    }
}
