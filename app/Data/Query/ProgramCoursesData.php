<?php

declare(strict_types=1);

namespace App\Data\Query;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use Spatie\LaravelData\Data;
use stdClass;

final class ProgramCoursesData extends Data
{
    public function __construct(
        public readonly int $curriculumID,
        public readonly int $sessionId,
        public readonly string $session,
        public readonly int $levelId,
        public readonly string $level,
        public readonly string $semester,
        public readonly int $courseId,
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly CreditUnit $creditUnit,
        public readonly CourseType $courseType,
        public readonly int $minElectiveUnit,
        public readonly int $minElectiveCount,
    ) {
    }

    public static function fromModel(stdClass $programCourse): self
    {
        return new self(
            curriculumID: $programCourse->id,
            sessionId: $programCourse->session_id,
            session: $programCourse->session,
            levelId: $programCourse->level_id,
            level: $programCourse->level,
            semester: $programCourse->semester,
            courseId: $programCourse->course_id,
            courseCode: $programCourse->course_code,
            courseTitle: $programCourse->course_title,
            creditUnit: CreditUnit::from($programCourse->credit_unit),
            courseType: CourseType::from($programCourse->course_type),
            minElectiveUnit: $programCourse->minimum_elective_units,
            minElectiveCount: $programCourse->minimum_elective_count,
        );
    }
}
