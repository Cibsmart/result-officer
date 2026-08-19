<?php

declare(strict_types=1);

namespace App\Imports\Excel;

use App\Models\ExcelImportEvent;
use Illuminate\Support\Carbon;

abstract class SpreadsheetImport
{
    private const int BATCH_SIZE = 1_000;

    /** @param array<string, string> $headings */
    final public function __construct(
        protected readonly ExcelImportEvent $event,
        protected readonly array $headings,
    ) {
    }

    /** @return class-string<\Illuminate\Database\Eloquent\Model> */
    abstract protected function model(): string;

    /**
     * The raw table attributes for a row, or null when the row carries no record.
     * @param array<string, string> $row
     * @return array<string, int|string|null>|null
     */
    abstract protected function mapRow(array $row): ?array;

    /** @param array<string, string> $headings */
    final public static function new(ExcelImportEvent $event, array $headings): static
    {
        return new static($event, $headings);
    }

    final public function import(string $filePath): void
    {
        $batch = [];

        foreach (Spreadsheet::rows($filePath) as $row) {
            $attributes = $this->mapRow($row);

            if ($attributes === null) {
                continue;
            }

            $batch[] = $attributes;

            if (count($batch) < self::BATCH_SIZE) {
                continue;
            }

            $this->insert($batch);

            $batch = [];
        }

        $this->insert($batch);
    }

    /** @param array<int, array<string, int|string|null>> $batch */
    private function insert(array $batch): void
    {
        if ($batch === []) {
            return;
        }

        $now = Carbon::now();

        $timestamps = ['created_at' => $now, 'updated_at' => $now];

        $model = new ($this->model())();

        $model->newQuery()->insert(array_map(
            static fn (array $attributes): array => array_merge($attributes, $timestamps),
            $batch,
        ));
    }
}
