<?php

declare(strict_types=1);

namespace App\Imports\Excel;

use DateInterval;
use DateTimeInterface;
use Generator;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;

final class Spreadsheet
{
    /**
     * The heading row, slugged the way every importer looks its columns up.
     * A blank heading falls back to its zero based column index.
     * @return array<int, int|string>
     */
    public static function headings(string $filePath): array
    {
        foreach (self::readSheet($filePath) as $values) {
            return self::slugHeadings($values);
        }

        return [];
    }

    /**
     * Every row below the heading row, keyed by the slugged heading of its column.
     * @return \Generator<int, array<string, string>>
     */
    public static function rows(string $filePath): Generator
    {
        $headings = null;

        foreach (self::readSheet($filePath) as $values) {
            if ($headings === null) {
                $headings = self::slugHeadings($values);

                continue;
            }

            yield self::keyByHeadings($values, $headings);
        }
    }

    /**
     * Only the first sheet is read; it is the one the heading validation inspects.
     * @return \Generator<int, array<int, bool|\DateInterval|\DateTimeInterface|float|int|string|null>>
     */
    private static function readSheet(string $filePath): Generator
    {
        $reader = new Reader();

        $reader->open($filePath);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    yield $row->toArray();
                }

                break;
            }
        } finally {
            $reader->close();
        }
    }

    /**
     * @param array<int, bool|\DateInterval|\DateTimeInterface|float|int|string|null> $values
     * @return array<int, int|string>
     */
    private static function slugHeadings(array $values): array
    {
        $headings = [];

        foreach ($values as $index => $value) {
            $heading = is_scalar($value)
                ? Str::slug((string) $value, '_')
                : '';

            $headings[$index] = $heading === ''
                ? $index
                : $heading;
        }

        return $headings;
    }

    /**
     * @param array<int, bool|\DateInterval|\DateTimeInterface|float|int|string|null> $values
     * @param array<int, int|string> $headings
     * @return array<string, string>
     */
    private static function keyByHeadings(array $values, array $headings): array
    {
        $row = [];

        foreach ($headings as $index => $heading) {
            $row[(string) $heading] = self::toString($values[$index] ?? null);
        }

        return $row;
    }

    /**
     * Importers work on string cells throughout, so dates are flattened to an
     * unambiguous, Carbon parsable form rather than handed over as objects.
     */
    private static function toString(bool|DateInterval|DateTimeInterface|float|int|string|null $value): string
    {
        return match (true) {
            $value === null, $value instanceof DateInterval => '',
            $value instanceof DateTimeInterface => $value->format('Y-m-d'),
            default => (string) $value,
        };
    }
}
