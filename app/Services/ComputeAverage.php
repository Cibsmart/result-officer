<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ComputeAverage
{
    public function __construct(private int $dividend, private int $divisor)
    {
    }

    public static function new(int|float $dividend, int $divisor): ComputeAverage
    {
        return new self((int) ($dividend * 1000), $divisor * 1000);
    }

    public function value(): float
    {
        if ($this->divisor === 0) {
            return 0.000;
        }

        return round($this->dividend / $this->divisor, 3);
    }
}
