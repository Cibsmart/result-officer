<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ComputationStrategy;
use Illuminate\Database\Eloquent\Model;

final class Institution extends Model
{
    /** @return array{strategy: 'App\Enums\ComputationStrategy'} */
    protected function casts(): array
    {
        return [
            'strategy' => ComputationStrategy::class,
        ];
    }
}
