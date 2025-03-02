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

final class ResultsImport implements ToModel, WithBatchInserts, WithCalculatedFormulas, WithChunkReading, WithHeadingRow
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
        $levelInput = Str::of($row[$this->headings['level']])->replace('LEVEL', '')->trim()->value();

        $level = $levelInput === ''
            ? Str::of($row[$this->headings['course_code']])->trim()->afterLast(' ')->value()[0] . '00'
            : $levelInput;

        return [
            'course_code' => Str::trim($row[$this->headings['course_code']]),
            'course_title' => Str::trim($row[$this->headings['course_title']]),
            'credit_unit' => (int) Str::trim($row[$this->headings['credit_unit']]),
            'department' => Str::of($row[$this->headings['department']])->replace('[None]', '')->trim()->value(),
            'exam' => (int) Str::trim($row[$this->headings['exam']]),
            'examiner' => Str::trim($row[$this->headings['examiner']]),
            //            'examiner_department' => Str::trim($row[$this->headings['examiner_department']]),
            'exam_date' => self::cleanDate($row[$this->headings['exam_date']]),
            'excel_import_event_id' => $this->event->id,
            //            'grade' => Str::trim($row[$this->headings['grade']]),
            'in_course' => (int) Str::trim($row[$this->headings['in_course']]),
            'in_course_2' => (int) Str::trim($row[$this->headings['in_course_2']]),
            'level' => $level,
            'name' => Str::trim($row[$this->headings['name']]),
            'registration_number' => Str::trim($row[$this->headings['registration_number']]),
            'semester' => Str::of($row[$this->headings['semester']])->replace('SEMESTER', '')->trim()->value(),
            'session' => Str::trim($row[$this->headings['session']]),
            'sn' => (int) Str::trim($row[$this->headings['sn']]),
            'total' => (int) Str::trim($row[$this->headings['total']]),
        ];
    }
}
