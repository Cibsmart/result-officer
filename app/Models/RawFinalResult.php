<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RawFinalResult extends Model
{
    /**
     * @param array<string, string> $row
     * @param array<string, string> $headings
     */
    public static function fromExcelRow(
        array $row,
        ExcelImportEvent $event,
        array $headings,
    ): self {
        $rawFinalResult = new self();

        $rawFinalResult->excel_import_event_id = $event->id;
        $rawFinalResult->sn = (int) $row[$headings['sn']];
        $rawFinalResult->name = $row[$headings['name']];
        $rawFinalResult->registration_number = $row[$headings['registration_number']];
        $rawFinalResult->in_course = (int) $row[$headings['in_course']];
        $rawFinalResult->exam = (int) $row[$headings['exam']];
        $rawFinalResult->total = (int) $row[$headings['total']];
        $rawFinalResult->grade = $row[$headings['grade']];
        $rawFinalResult->credit_unit = (int) $row[$headings['credit_unit']];
        $rawFinalResult->semester = $row[$headings['semester']];
        $rawFinalResult->session = $row[$headings['session']];
        $rawFinalResult->course_code = $row[$headings['course_code']];
        $rawFinalResult->course_title = $row[$headings['course_title']];
        $rawFinalResult->department = $row[$headings['department']];
        $rawFinalResult->examiner = $row[$headings['examiner']];
        $rawFinalResult->examiner_department = $row[$headings['examiner_department']];
        $rawFinalResult->exam_date = $row[$headings['exam_date']];
        $rawFinalResult->year = $row[$headings['year']];
        $rawFinalResult->month = $row[$headings['month']];
        $rawFinalResult->originating_session = $row[$headings['originating_session']];
        $rawFinalResult->database_officer = $row[$headings['database_officer']];
        $rawFinalResult->exam_officer = $row[$headings['exam_officer']];

        $oldRegistrationNumber = $row[$headings['old_registration_number']];
        $rawFinalResult->old_registration_number = $oldRegistrationNumber === 'NIL' || $oldRegistrationNumber === ''
            ? null
            : $oldRegistrationNumber;

        $rawFinalResult->save();

        return $rawFinalResult;
    }
}
