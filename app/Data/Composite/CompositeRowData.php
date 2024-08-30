<?php

declare(strict_types=1);

namespace App\Data\Composite;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class CompositeRowData extends Data
{
    public function __construct(
        public readonly int $studentId,
        public readonly string $studentName,
        public readonly string $registrationNumber,
        public readonly string $creditUnitTotal,
        public readonly string $gradePointTotal,
        public readonly string $gradePointAverage,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseData> */
        public readonly Collection $levelCourses,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseData> */
        public readonly Collection $otherCourses,
    ) {
    }

    /**
     * @param array{id: int, name: string, registrationNumber: string, creditUnitTotal: int, gradePointTotal:int,
     *     gradePointAverage: string, levelCourses: \Illuminate\Support\Collection<int,
     *     \App\Data\Composite\CompositeCourseData>, otherCourses: \Illuminate\Support\Collection<int,
     *     \App\Data\Composite\CompositeCourseData>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            studentId: $data['id'],
            studentName: $data['name'],
            registrationNumber: $data['registrationNumber'],
            creditUnitTotal: (string) $data['creditUnitTotal'],
            gradePointTotal: (string) $data['gradePointTotal'],
            gradePointAverage: $data['gradePointAverage'],
            levelCourses: $data['levelCourses'],
            otherCourses: $data['otherCourses'],
        );
    }
}
