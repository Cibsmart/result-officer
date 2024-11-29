<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CumulativeComputationStrategy;
use Illuminate\Database\Eloquent\Model;

final class Institution extends Model
{
    /** @return array{strategy: 'App\Enums\CumulativeComputationStrategy'} */
    protected function casts(): array
    {
        return [
            'strategy' => CumulativeComputationStrategy::class,
        ];
    }
}
