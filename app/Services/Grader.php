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
            $this->withinRangeOf(Grade::A) => Grade::A,
            $this->withinRangeOf(Grade::B) => Grade::B,
            $this->withinRangeOf(Grade::C) => Grade::C,
            $this->withinRangeOf(Grade::D) => Grade::D,
            $this->isEGradeAllowed && ($this->withinRangeOf(Grade::E)) => Grade::E,
            default => Grade::F,
        };
    }

    private function withinRangeOf(Grade $grade): bool
    {
        return $this->score->value >= $grade->min()
            && $this->score->value <= $grade->max();
    }
}
