<?php

declare(strict_types=1);

namespace App\Data\Query;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use Spatie\LaravelData\Data;
use stdClass;

final class StudentCoursesData extends Data
{
    public function __construct(
        public readonly int $studentId,
        public readonly int $registrationId,
        public readonly int $sessionId,
        public readonly string $session,
        public readonly string $semester,
        public readonly int $courseId,
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly CreditUnit $creditUnit,
        public readonly CourseStatus $courseStatus,
        public readonly ?int $totalScore,
        public readonly ?Grade $grade,
        public readonly ?int $gradePoint,
        public readonly ?int $programCurriculumCourseId,
    ) {
    }

    public static function fromModel(stdClass $result): self
    {
        return new self(
            studentId: $result->id,
            registrationId: $result->registration_id,
            sessionId: $result->session_id,
            session: $result->session,
            semester: $result->semester,
            courseId: $result->course_id,
            courseCode: $result->course_code,
            courseTitle: $result->course_title,
            creditUnit: CreditUnit::from($result->credit_unit),
            courseStatus: CourseStatus::from($result->course_status),
            totalScore: $result->total_score,
            grade: $result->grade === null ? null : Grade::from($result->grade),
            gradePoint: $result->grade_point,
            programCurriculumCourseId: $result->program_curriculum_course_id,
        );
    }
}
