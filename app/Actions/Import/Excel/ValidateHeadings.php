<?php

declare(strict_types=1);

namespace App\Actions\Import\Excel;

use App\Enums\ExcelImportType;

final class ValidateHeadings
{
    public const int THRESHOLD = 80;

    /**
     * @param array<int, int|string> $headings
     * @return array{passed: bool, validated: array<string, string>, missing: string}
     */
    public function execute(array $headings, ExcelImportType $type): array
    {
        $expectedHeadings = $type->expectedHeadings();

        $headings = array_filter($headings, fn (string|int $header) => is_string($header));

        $validatedHeadings = [];

        foreach ($headings as $heading) {
            $validatedHeadingKey = $this->getBestMatchingValidHeadingKey($heading, $expectedHeadings);

            if ($validatedHeadingKey === null) {
                continue;
            }

            $validatedHeadings[$validatedHeadingKey] = $heading;
        }

        $missingHeadings = array_keys(array_diff_key($expectedHeadings, $validatedHeadings));

        $passedValidation = count($validatedHeadings) === count($expectedHeadings);

        return [
            'missing' => collect($missingHeadings)->join(', '),
            'passed' => $passedValidation,
            'validated' => $validatedHeadings,
        ];
    }

    /** @param array<string, array<int, string>> $expectedHeadings */
    private function getBestMatchingValidHeadingKey(string $heading, array $expectedHeadings): ?string
    {
        $bestMatch = null;
        $bestMatchPercentage = 0.0;

        foreach ($expectedHeadings as $key => $possibleNames) {
            [$hasMatch, $highestMatchScore] = $this->getMatches($heading, $possibleNames);

            if (! $hasMatch || $highestMatchScore <= $bestMatchPercentage) {
                continue;
            }

            $bestMatch = $key;
            $bestMatchPercentage = $highestMatchScore;
        }

        return $bestMatch;
    }

    /**
     * @param array<int, string> $possibleNames
     * @return array<int, bool|float>
     */
    private function getMatches(string $heading, array $possibleNames): array
    {
        $highestSimilarity = 0.0;

        foreach ($possibleNames as $name) {
            similar_text($heading, $name, $percent);

            if ($percent <= $highestSimilarity) {
                continue;
            }

            $highestSimilarity = $percent;
        }

        return [$highestSimilarity >= self::THRESHOLD, $highestSimilarity];
    }
}
