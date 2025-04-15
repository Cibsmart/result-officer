<?php

declare(strict_types=1);

namespace App\Data\Models;

use Spatie\LaravelData\Data;

final class ImportEventCountData extends Data
{
    public function __construct(
        public readonly int $saved,
        public readonly int $processed,
        public readonly int $duplicate,
        public readonly int $failed,
        public readonly int $pending,
    ) {
    }
}
