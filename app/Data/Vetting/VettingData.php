<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Data\Students\StudentBasicInfoData;
use Spatie\LaravelData\Data;

final class VettingData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly StudentBasicInfoData $student,
    ) {
    }
}
