<?php

declare(strict_types=1);

namespace App\Values;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class CourseTitle
{
    public function __construct(public string $value)
    {
        if ($this->value === '') {
            throw new InvalidArgumentException('Invalid course title');
        }
    }

    public static function new(string $title): self
    {
        return new self(self::cleanTitle($title));
    }

    private static function cleanTitle(string $title): string
    {
        return Str::of($title)
            ->trim()
            ->replace('  ', ' ')
            ->replace(' & ', ' and ')
            ->upper()
            ->value();
    }
}
