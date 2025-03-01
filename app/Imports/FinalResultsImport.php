<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ExcelImportEvent;
use App\Models\RawFinalResult;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

final class FinalResultsImport implements ToModel, WithBatchInserts, WithCalculatedFormulas, WithChunkReading, WithHeadingRow
{
    use Importable;

    private const int CHUNK_SIZE = 1_000;

    /** @param array<string, string> $headings */
    public function __construct(private readonly ExcelImportEvent $event, private readonly array $headings)
    {
    }

    /** @param array<string, string> $headings */
    public static function new(ExcelImportEvent $event, array $headings): self
    {
        return new self($event, $headings);
    }

    /**
     * @param array<string, string> $row
     * {@inheritDoc}
     */
    public function model(array $row): ?RawFinalResult
    {
        if (
            ! isset($row[$this->headings['registration_number']])
            || $row[$this->headings['registration_number']] === ''
        ) {
            return null;
        }

        return new RawFinalResult($this->mapRowToModel($row));
    }

    /** {@inheritDoc} */
    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    /** {@inheritDoc} */
    public function batchSize(): int
    {
        return self::CHUNK_SIZE;
    }

    private static function cleanRegistrationId(string|int|null $registrationId): int
    {
        if (is_null($registrationId) || $registrationId === '') {
            return 0;
        }

        return (int) $registrationId;
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

    /**
     * @param array<string, string> $row
     * @return array<string, int|string|null>
     */
    private function mapRowToModel(array $row): array
    {
        $oldRegistrationNumber = Str::trim($row[$this->headings['old_registration_number']]);

        $oldRegNumber = $oldRegistrationNumber === 'NIL' || $oldRegistrationNumber === ''
            ? null
            : $oldRegistrationNumber;

        return [
            'course_code' => Str::trim($row[$this->headings['course_code']]),
            'course_title' => Str::trim($row[$this->headings['course_title']]),
            'credit_unit' => (int) Str::trim($row[$this->headings['credit_unit']]),
            'database_officer' => Str::trim($row[$this->headings['database_officer']]),
            'department' => Str::trim($row[$this->headings['department']]),
            'exam' => (int) Str::trim($row[$this->headings['exam']]),
            'examiner' => Str::trim($row[$this->headings['examiner']]),
            'examiner_department' => Str::trim($row[$this->headings['examiner_department']]),
            'exam_date' => self::cleanDate($row[$this->headings['exam_date']]),
            'exam_officer' => Str::trim($row[$this->headings['exam_officer']]),
            'excel_import_event_id' => $this->event->id,
            'grade' => Str::trim($row[$this->headings['grade']]),
            'in_course' => (int) Str::trim($row[$this->headings['in_course']]),
            'in_course_2' => (int) Str::trim($row[$this->headings['in_course_2']]),
            'level' => Str::of($row[$this->headings['course_code']])->trim()->afterLast(' ')[0] . '00',
            'month' => Str::trim($row[$this->headings['month']]),
            'name' => Str::trim($row[$this->headings['name']]),
            'old_registration_number' => $oldRegNumber,
            'originating_session' => Str::trim($row[$this->headings['originating_session']]),
            'registration_id' => self::cleanRegistrationId(Str::trim($row[$this->headings['id']])),
            'registration_number' => Str::trim($row[$this->headings['registration_number']]),
            'semester' => Str::trim($row[$this->headings['semester']]),
            'session' => Str::trim($row[$this->headings['session']]),
            'sn' => (int) Str::trim($row[$this->headings['sn']]),
            'total' => (int) Str::trim($row[$this->headings['total']]),
            'year' => Str::trim($row[$this->headings['year']]),
        ];
    }
}
