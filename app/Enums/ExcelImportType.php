<?php

declare(strict_types=1);

namespace App\Enums;

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

    /** @return array<string, array<int, string>> */
    private function getCurriculumHeadings(): array
    {
        return [];
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
