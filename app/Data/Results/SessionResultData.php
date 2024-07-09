<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Enrollment;
use App\Models\Semester;
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
        $first = Semester::query()->findOrFail(1);
        $second = Semester::query()->findOrFail(2);

        $semesterResults = collect(SemesterResultData::collect([
            SemesterResultData::fromModel($first, $enrollment),
            SemesterResultData::fromModel($second, $enrollment),
        ]));

        $cumulativeGradePointAverage = round(
            $semesterResults->sum('gradePointAverage') / $semesterResults->count(),
            3,
        );

        return new self($semesterResults, $enrollment->session->name, $cumulativeGradePointAverage);
    }
}
