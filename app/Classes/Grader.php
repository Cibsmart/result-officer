<?php

namespace App\Classes;

use App\Enums\Grade;

readonly class Grader
{
    public function __construct(private Score $score)
    {
    }

    public function grade(): Grade
    {
        if ($this->score->total() >= 70 && $this->score->total() <= 100) {
            return Grade::A;
        }

        if ($this->score->total() >= 60 && $this->score->total() < 70) {
            return Grade::B;
        }

        if ($this->score->total() >= 50 && $this->score->total() < 60) {
            return Grade::C;
        }

        if ($this->score->total() >= 45 && $this->score->total() < 50) {
            return Grade::D;
        }

        if ($this->score->total() >= 40 && $this->score->total() < 45) {
            return Grade::E;
        }

        return Grade::F;
    }
}
