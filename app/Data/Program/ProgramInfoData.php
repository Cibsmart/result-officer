<?php

declare(strict_types=1);

namespace App\Data\Program;

use Spatie\LaravelData\Data;

final class ProgramInfoData extends Data
{
    public function __construct(
        public readonly string $programName,
        public readonly string $departmentName,
        public readonly string $facultyName,
    ) {
    }
}
