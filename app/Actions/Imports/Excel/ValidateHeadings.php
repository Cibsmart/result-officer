<?php

declare(strict_types=1);

namespace App\Actions\Imports\Excel;

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
        $validatedScores = [];

        foreach ($headings as $heading) {
            [$validatedHeadingKey, $matchScore] = $this->getBestMatchingValidHeadingKey($heading, $expectedHeadings);

            if ($validatedHeadingKey === null) {
                continue;
            }

            // Keep the heading that matches a field best. A weaker later match
            // (e.g. "old_registration_number" at 90%) must never overwrite a
            // stronger earlier one (e.g. "registration_number" at 100%).
            if (array_key_exists($validatedHeadingKey, $validatedScores)
                && $matchScore <= $validatedScores[$validatedHeadingKey]
            ) {
                continue;
            }

            $validatedHeadings[$validatedHeadingKey] = $heading;
            $validatedScores[$validatedHeadingKey] = $matchScore;
        }

        $missingHeadings = array_keys(array_diff_key($expectedHeadings, $validatedHeadings));

        $passedValidation = count($validatedHeadings) === count($expectedHeadings);

        return [
            'missing' => collect($missingHeadings)->join(', '),
            'passed' => $passedValidation,
            'validated' => $validatedHeadings,
        ];
    }

    /**
     * @param array<string, array<int, string>> $expectedHeadings
     * @return array{0: string|null, 1: float}
     */
    private function getBestMatchingValidHeadingKey(string $heading, array $expectedHeadings): array
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

        return [$bestMatch, $bestMatchPercentage];
    }

    /**
     * @param array<int, string> $possibleNames
     * @return array{bool, float}
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
