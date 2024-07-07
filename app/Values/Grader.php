<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\Grade;

final readonly class Grader
{
    public function __construct(private Score $score)
    {
    }

    public function grade(): Grade
    {
        if ($this->score->value() >= 70 && $this->score->value() <= 100) {
            return Grade::A;
        }

        if ($this->score->value() >= 60 && $this->score->value() < 70) {
            return Grade::B;
        }

        if ($this->score->value() >= 50 && $this->score->value() < 60) {
            return Grade::C;
        }

        if ($this->score->value() >= 45 && $this->score->value() < 50) {
            return Grade::D;
        }

        if ($this->score->value() >= 40 && $this->score->value() < 45) {
            return Grade::E;
        }

        return Grade::F;
    }
}
