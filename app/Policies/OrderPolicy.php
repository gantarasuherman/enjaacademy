<?php

declare(strict_types=1);

namespace App\Policies;

class OrderPolicy extends BasePolicy
{
    protected function prefix(): string
    {
        return 'orders';
    }
}
