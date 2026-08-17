<?php

declare(strict_types=1);

namespace App\Policies;

class VocabularyWordPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'vocabulary-words';
    }
}
