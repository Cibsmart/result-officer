<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Enums\Grade;
use Spatie\LaravelData\Data;

final class GradingSchemeData extends Data
{
    public function __construct(
        public readonly string $range,
        public readonly string $interpretation,
        public readonly string $grade,
        public readonly int $gradePoint,
    ) {
    }

    public static function fromModel(Grade $grade, bool $isEGradeAllowed = true): self
    {
        $min = $grade->min();
        $max = ! $isEGradeAllowed && $grade === Grade::F
            ? Grade::E->max()
            : $grade->max();

        return new self(
            range: "$min - $max",
            interpretation: $grade->interpretation(),
            grade: $grade->name,
            gradePoint: $grade->value,
        );
    }
}
