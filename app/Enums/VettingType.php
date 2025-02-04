<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingType: string
{
    case ORGANIZE_STUDY_YEAR = 'organize_year';
    case VALIDATE_RESULTS = 'validate_result';
    case MATCH_SEMESTERS = 'match_semesters';
    case CHECK_SEMESTER_CREDIT_LOADS = 'semester_credit';
    case MATCH_COURSES = 'match_courses';
    case CHECK_CREDIT_UNITS = 'credit_units';
    case CHECK_FIRST_YEAR_COURSES = 'first_year';
    case CHECK_CORE_COURSES = 'core_courses';
    case CHECK_ELECTIVE_COURSES = 'elective_courses';
    case CHECK_FAILED_COURSES = 'failed_courses';

    public function description(VettingStatus $status): string
    {
        return match ($this) {
            self::ORGANIZE_STUDY_YEAR => $this->getOrganizeStudyYearMessage($status),
            self::VALIDATE_RESULTS => $this->getValidateResultMessage($status),
            self::MATCH_SEMESTERS => $this->getMatchSemesterMessage($status),
            self::CHECK_SEMESTER_CREDIT_LOADS => $this->getSemesterLoadCheckMessage($status),
            self::MATCH_COURSES => $this->getMatchCourseMessage($status),
            self::CHECK_FIRST_YEAR_COURSES => $this->getFirstYearCheckMessage($status),
            self::CHECK_CREDIT_UNITS => $this->getCreditUnitCheckMessage($status),
            self::CHECK_CORE_COURSES => $this->getCoreCourseCheckMessage($status),
            self::CHECK_ELECTIVE_COURSES => $this->getElectiveCourseCheckMessage($status),
            self::CHECK_FAILED_COURSES => $this->getFailedCourseCheckMessage($status),
        };
    }

    private function getOrganizeStudyYearMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Study years are organized',
            VettingStatus::FAILED => 'Study years not organized',
            VettingStatus::UNCHECKED => 'Session enrollments not found',
            default => 'pending',
        };
    }

    private function getValidateResultMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Results are valid',
            VettingStatus::FAILED => 'The following results are invalid:',
            VettingStatus::UNCHECKED => 'Results not found',
            default => 'pending',
        };
    }

    private function getMatchSemesterMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Semester matches a semester in the curriculum',
            VettingStatus::FAILED => 'The following semesters do not match any curriculum semester:',
            VettingStatus::UNCHECKED => 'Curriculum not found',
            default => 'pending',
        };
    }

    private function getSemesterLoadCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Semester credit loads are within limits',
            VettingStatus::FAILED => 'The credit loads for the following semester are not within limits:',
            VettingStatus::UNCHECKED => 'Semester enrollments not found',
            default => 'pending',
        };
    }

    private function getMatchCourseMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Courses matches a course in the curriculum',
            VettingStatus::FAILED => 'The following courses do not match any curriculum course:',
            VettingStatus::UNCHECKED => 'Curriculum not found',
            default => 'pending',
        };
    }

    private function getFirstYearCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Registered all first year courses in the first year of study',
            VettingStatus::FAILED => 'The following first-year courses were not taken in the first year:',
            VettingStatus::UNCHECKED => 'Courses not matched',
            default => 'pending',
        };
    }

    private function getCreditUnitCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Course credit units match curriculum course units',
            VettingStatus::FAILED => 'The credit units for these courses differ from the curriculum:',
            VettingStatus::UNCHECKED => 'Courses not matched',
            default => 'pending',
        };
    }

    private function getCoreCourseCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Attempted all Core, Required and General Courses',
            VettingStatus::FAILED => 'The following core (C), required (R) and general (G) courses were not taken:',
            VettingStatus::UNCHECKED => 'Course not matched',
            default => 'pending',
        };
    }

    private function getElectiveCourseCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Attempted necessary elective courses',
            VettingStatus::FAILED => 'The following elective requirements not satisfied:',
            VettingStatus::UNCHECKED => 'Courses not matched',
            default => 'pending',
        };
    }

    private function getFailedCourseCheckMessage(VettingStatus $status): string
    {
        return match ($status) {
            VettingStatus::PASSED => 'Passed all attempted courses',
            VettingStatus::FAILED => 'The following failed courses are not yet passed:',
            VettingStatus::UNCHECKED => 'Results not found',
            default => 'pending',
        };
    }
}
