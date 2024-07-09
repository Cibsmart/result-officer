<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Enrollment;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SessionResultData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<\App\Data\Results\SemesterResultData> */
        public Collection $semesterResults,
        public string $session,
        public float $cummulativeGradePointAverage,
    ) {
    }

    public static function fromModel(Enrollment $enrollment): self
    {
        $semesters = SemesterResultData::collect($enrollment->semesters);

        $cumulativeGradePointAverage = round(
            $semesters->sum('gradePointAverage') / $semesters->count(),
            3,
        );

        return new self($semesters, $enrollment->session->name, $cumulativeGradePointAverage);
    }
}
