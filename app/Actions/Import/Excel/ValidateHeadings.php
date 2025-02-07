<?php

declare(strict_types=1);

namespace App\Actions\Import\Excel;

use App\Enums\ExcelImportType;

final class ValidateHeadings
{
    public const int THRESHOLD = 80;

    /**
     * @param array<int, int|string> $headings
     * @return array{bool, array<string, string>, string}
     */
    public function execute(array $headings, ExcelImportType $type): array
    {
        $expectedHeadings = $type->expectedHeadings();

        $headings = array_filter($headings, fn (string|int $header) => is_string($header));

        $validatedHeadings = [];

        foreach ($headings as $heading) {
            $validatedHeadingKey = $this->getValidHeadingKey($heading, $expectedHeadings);

            if ($validatedHeadingKey === null) {
                continue;
            }

            $validatedHeadings[$validatedHeadingKey] = $heading;
        }

        $missingHeadings = array_keys(array_diff_key($expectedHeadings, $validatedHeadings));

        $passedValidation = count($validatedHeadings) === count($expectedHeadings);

        return [$passedValidation, $validatedHeadings, collect($missingHeadings)->join(', ')];
    }

    /** @param array<string, array<int, string>> $expectedHeadings */
    private function getValidHeadingKey(string $heading, array $expectedHeadings): ?string
    {
        return array_find_key($expectedHeadings, fn (array $alternatives) => $this->hasMatch($heading, $alternatives));
    }

    /** @param array<int, string> $possibleNames */
    private function hasMatch(string $heading, array $possibleNames): bool
    {
        $highestSimilarity = 0;

        foreach ($possibleNames as $name) {
            similar_text($heading, $name, $percent);

            if ($percent <= $highestSimilarity) {
                continue;
            }

            $highestSimilarity = $percent;
        }

        return $highestSimilarity >= self::THRESHOLD;
    }
}
