<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Grade;
use App\Values\TotalScore;

final readonly class Grader
{
    public function __construct(private TotalScore $score, private bool $isEGradeAllowed)
    {
    }

    public static function new(TotalScore $score, bool $isEGradeAllowed): self
    {
        return new self($score, $isEGradeAllowed);
    }

    public function grade(): Grade
    {
        return match (true) {
            $this->score->value >= 70 && $this->score->value <= 100 => Grade::A,
            $this->score->value >= 60 && $this->score->value < 70 => Grade::B,
            $this->score->value >= 50 && $this->score->value < 60 => Grade::C,
            $this->score->value >= 45 && $this->score->value < 50 => Grade::D,
            $this->score->value >= 40 && $this->score->value < 45 && $this->isEGradeAllowed => Grade::E,
            default => Grade::F,
        };
    }
}
