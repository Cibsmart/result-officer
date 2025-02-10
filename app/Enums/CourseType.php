<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Str;

enum CourseType: string
{
    case CORE = 'C';
    case ELECTIVE = 'E';
    case GENERAL = 'G';
    case ANCILLARY = 'A';
    case REQUIRED = 'R';

    public static function fromNameOrCode(string $value): ?self
    {
        $value = Str::upper($value);

        foreach (self::cases() as $case) {
            if (in_array($value, [$case->value, $case->name], true)) {
                return $case;
            }
        }

        return self::highestMatch($value);
    }

    private static function highestMatch(string $value): ?self
    {
        $bestMatch = null;
        $highestSimilarity = 0.0;

        foreach (self::cases() as $case) {
            similar_text($value, $case->name, $percent);

            if ($percent <= $highestSimilarity) {
                continue;
            }

            $highestSimilarity = $percent;
            $bestMatch = $case;
        }

        return $highestSimilarity >= 80
            ? $bestMatch
            : null;
    }
}
