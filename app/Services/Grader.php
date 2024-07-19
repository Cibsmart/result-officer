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
            $this->score->value >= Grade::A->min() && $this->score->value <= Grade::A->max() => Grade::A,
            $this->score->value >= Grade::B->min() && $this->score->value < Grade::A->min() => Grade::B,
            $this->score->value >= Grade::C->min() && $this->score->value < Grade::B->min() => Grade::C,
            $this->score->value >= Grade::D->min() && $this->score->value < Grade::C->min() => Grade::D,
            $this->isEGradeAllowed &&
            ($this->score->value >= Grade::E->min() && $this->score->value < Grade::D->min()) => Grade::E,
            default => Grade::F,
        };
    }
}
