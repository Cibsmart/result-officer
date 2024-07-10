<?php

declare(strict_types=1);

namespace App\Services;

use App\Values\MatriculationNumber;
use Illuminate\Support\Str;

final readonly class GradingSystem
{
    public function __construct(public MatriculationNumber $matriculationNumber)
    {
    }

    public static function new(MatriculationNumber $matriculationNumber): self
    {
        return new self($matriculationNumber);
    }

    public function isEGradeAllowed(): bool
    {
        return ! in_array($this->getMatriculationNumberYear(), [2013, 2014, 2015, 2016, 2017]);
    }

    public function getMatriculationNumberYear(): int
    {
        return (int) Str::of($this->matriculationNumber->value)->explode('/')[1];
    }
}
