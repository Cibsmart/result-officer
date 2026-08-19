<?php

declare(strict_types=1);

namespace App\Imports;

use App\Imports\Excel\SpreadsheetImport;
use App\Models\RawExcelResult;
use Illuminate\Support\Str;

final class ResultsImport extends SpreadsheetImport
{
    /** {@inheritDoc} */
    protected function model(): string
    {
        return RawExcelResult::class;
    }

    /** {@inheritDoc} */
    protected function mapRow(array $row): ?array
    {
        if (
            ! isset($row[$this->headings['registration_number']])
            || $row[$this->headings['registration_number']] === ''
        ) {
            return null;
        }

        return $this->mapRowToModel($row);
    }

    private static function cleanDate(string $date): ?string
    {
        // A bare number is an Excel date serial rather than a date we can trust.
        if (is_numeric($date)) {
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

    /**
     * @param array<string, string> $row
     * @return array<string, int|string|null>
     */
    private function mapRowToModel(array $row): array
    {
        $level = array_key_exists('level', $row)
            ? Str::of($row['level'])->replace('LEVEL', '')->trim()->value()
            : Str::of($row[$this->headings['course_code']])->trim()->afterLast(' ')->value()[0] . '00';

        $registrationNumber = Str::of($row[$this->headings['registration_number']])
            ->replace('*', '')
            ->replace('`', '')
            ->replace('/DE', '')
            ->trim()->value();

        $courseCode = Str::of($row[$this->headings['course_code']])
            ->replace('PHIL', 'PHL')
            ->replace('ENGL', 'ENG')
            ->trim()->value();

        return [
            'course_code' => $courseCode,
            'course_title' => Str::trim($row[$this->headings['course_title']]),
            'credit_unit' => (int) Str::trim($row[$this->headings['credit_unit']]),
            'department' => Str::of($row[$this->headings['department']])->replace('[None]', '')->trim()->value(),
            'exam' => (int) Str::trim($row[$this->headings['exam']]),
            'examiner' => Str::trim($row[$this->headings['examiner']]),
            'examiner_department' => Str::trim($row[$this->headings['examiner_department']]),
            'exam_date' => self::cleanDate($row[$this->headings['exam_date']]),
            'excel_import_event_id' => $this->event->id,
            'grade' => Str::trim($row[$this->headings['grade']]),
            'in_course' => (int) Str::trim($row[$this->headings['in_course']]),
            'in_course_2' => (int) Str::trim($row[$this->headings['in_course_2']]),
            'level' => $level,
            'name' => Str::trim($row[$this->headings['name']]),
            'registration_number' => $registrationNumber,
            'semester' => Str::of($row[$this->headings['semester']])->replace('SEMESTER', '')->trim()->value(),
            'session' => Str::trim($row[$this->headings['session']]),
            'sn' => (int) Str::trim($row[$this->headings['sn']]),
            'total' => (int) Str::trim($row[$this->headings['total']]),
        ];
    }
}
