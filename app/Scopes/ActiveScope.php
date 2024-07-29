<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

final readonly class ActiveScope
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Faculty|\App\Models\Department|\App\Models\Program> $builder
     */
    public function __invoke(Builder $builder): void
    {
        $builder->where('is_active', true);
    }
}
