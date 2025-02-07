<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        $rawFinalResult->sn = (int) Str::trim($row[$headings['sn']]);
        $rawFinalResult->name = Str::trim($row[$headings['name']]);
        $rawFinalResult->registration_number = Str::trim($row[$headings['registration_number']]);
        $rawFinalResult->in_course = (int) Str::trim($row[$headings['in_course']]);
        $rawFinalResult->exam = (int) Str::trim($row[$headings['exam']]);
        $rawFinalResult->total = (int) Str::trim($row[$headings['total']]);
        $rawFinalResult->grade = Str::trim($row[$headings['grade']]);
        $rawFinalResult->credit_unit = (int) Str::trim($row[$headings['credit_unit']]);
        $rawFinalResult->semester = Str::trim($row[$headings['semester']]);
        $rawFinalResult->session = Str::trim($row[$headings['session']]);
        $rawFinalResult->level = Str::of($row[$headings['course_code']])->trim()->afterLast(' ')[0] . '00';
        $rawFinalResult->course_code = Str::trim($row[$headings['course_code']]);
        $rawFinalResult->course_title = Str::trim($row[$headings['course_title']]);
        $rawFinalResult->department = Str::trim($row[$headings['department']]);
        $rawFinalResult->examiner = Str::trim($row[$headings['examiner']]);
        $rawFinalResult->exam_date = self::cleanDate($row[$headings['exam_date']]);
        $rawFinalResult->examiner_department = Str::trim($row[$headings['examiner_department']]);
        $rawFinalResult->year = Str::trim($row[$headings['year']]);
        $rawFinalResult->month = Str::trim($row[$headings['month']]);
        $rawFinalResult->originating_session = Str::trim($row[$headings['originating_session']]);
        $rawFinalResult->database_officer = Str::trim($row[$headings['database_officer']]);
        $rawFinalResult->exam_officer = Str::trim($row[$headings['exam_officer']]);

        $oldRegistrationNumber = Str::trim($row[$headings['old_registration_number']]);
        $rawFinalResult->old_registration_number = $oldRegistrationNumber === 'NIL' || $oldRegistrationNumber === ''
            ? null
            : $oldRegistrationNumber;

        $rawFinalResult->save();

        return $rawFinalResult;
    }

    private static function cleanDate(string|int|null $date): ?string
    {
        if (! is_string($date)) {
            return null;
        }

        $value = Str::of($date)
            ->replace('/', '-')
            ->replace('(', '')
            ->replace(')', '')
            ->replace('"', '')
            ->replace("\'", '')
            ->replace('.', '')
            ->replace(' ', '')
            ->trim()
            ->value();

        return $value === ''
            ? null
            : $value;
    }
}
