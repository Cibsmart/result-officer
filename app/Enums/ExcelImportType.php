<?php

declare(strict_types=1);

namespace App\Enums;

use App\Imports\CurriculumCoursesImport;
use App\Imports\FinalResultsImport;

enum ExcelImportType: string
{
    case CURRICULUM = 'curriculum';
    case FINAL_RESULT = 'final_result';

    /** @return array<string, array<int, string>> */
    public function expectedHeadings(): array
    {
        return match ($this) {
            self::CURRICULUM => $this->getCurriculumHeadings(),
            self::FINAL_RESULT => $this->getFinalResultsHeadings(),
        };
    }

    public function getImportClass(): string
    {
        return match ($this) {
            self::CURRICULUM => CurriculumCoursesImport::class,
            self::FINAL_RESULT => FinalResultsImport::class,
        };
    }

    /** @return array<string, array<int, string>> */
    private function getCurriculumHeadings(): array
    {
        return [
            'course_code' => ['course_code', 'code'],
            'course_title' => ['course_title', 'title'],
            'course_type' => ['course_type', 'type'],
            'credit_unit' => ['credit_unit', 'credit_load', 'cload', 'cunit', 'credit_unit_load'],
            'curriculum' => ['curriculum', 'curriculum_name', 'curriculum_type'],
            'elective_group' => ['elective_group', 'elective_group_name', 'elective_group_code'],
            'entry_mode' => ['entry_mode', 'mode_of_entry'],
            'entry_session' => ['session', 'entry_session'],
            'level' => ['level'],
            'minimum_elective_count' => ['minimum_elective_count', 'minimum_number_of_elective'],
            'minimum_elective_unit' => ['minimum_elective_unit', 'minimum_elective_unit_load'],
            'program' => ['program', 'program_name', 'program_code'],
            'semester' => ['semester'],
        ];
    }

    /** @return array<string, array<int, string>> */
    private function getFinalResultsHeadings(): array
    {
        return [
            'course_code' => ['course_code', 'code'],
            'course_title' => ['course_title', 'title'],
            'credit_unit' => ['credit_unit', 'credit_load', 'cload', 'cunit', 'credit_unit_load'],
            'database_officer' => ['database_officer', 'database_co'],
            'department' => ['department', 'dept', 'students_dept', 'std_dept'],
            'exam' => ['exam', 'exam_score'],
            'examiner' => ['examiner', 'examiners_name'],
            'examiner_department' => ['examiners_department', 'examiners_dept', 'exam_dept'],
            'exam_date' => ['exam_date', 'date_of_exam'],
            'exam_officer' => ['exam_officer', 'exams_co'],
            'grade' => ['grade'],
            'in_course' => ['in_course', 'in_course_score', 'inc', 'inc_score', 'inc_ass', 'inc_assessment'],
            'month' => ['month'],
            'name' => ['name', 'students_name', 'name_of_students'],
            'old_registration_number' => ['old_registration_number', 'old_reg_number', 'old_reg_no'],
            'originating_session' => ['originating_session'],
            'registration_number' => ['registration_number', 'reg_number', 'reg_no'],
            'semester' => ['semester'],
            'session' => ['session'],
            'sn' => ['sn', 'serial_number'],
            'total' => ['total', 'total_score'],
            'year' => ['year'],
        ];
    }
}
