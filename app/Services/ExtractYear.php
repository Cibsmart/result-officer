<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

final readonly class ExtractYear
{
    public function __construct(public string $registrationNumber)
    {
    }

    public static function fromRegistrationNumber(string $registrationNumber): self
    {
        return new self($registrationNumber);
    }

    public function session(): string
    {
        $year = $this->year();
        $next = $year + 1;

        return Str::of((string) $year)
            ->append('/')
            ->append((string) $next)
            ->value();
    }

    public function year(): int
    {
        $year = Str::of($this->registrationNumber)->explode('/')[1];

        return (int) $year;
    }
}
