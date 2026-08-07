<?php

declare(strict_types=1);

namespace App\Policies;

class LanguagePolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'languages';
    }
}
