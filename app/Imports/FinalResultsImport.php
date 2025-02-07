<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ExcelImportEvent;
use App\Models\RawFinalResult;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

final class FinalResultsImport implements ToModel, WithCalculatedFormulas, WithHeadingRow
{
    use Importable;

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
    public function model(array $row): RawFinalResult
    {
        return RawFinalResult::fromExcelRow($row, $this->event, $this->headings);
    }
}
