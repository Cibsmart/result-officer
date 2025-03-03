<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\StatusColor;
use App\Enums\StudentStatus;
use Spatie\LaravelData\Data;

final class StudentBasicInfoData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $registrationNumber,
        public readonly string $name,
        public readonly string $departmentProgram,
        public readonly string $slug,
        public readonly StudentStatus $status,
        public readonly StatusColor $statusColor,
    ) {
    }
}
