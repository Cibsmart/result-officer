<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\Grade;
use InvalidArgumentException;

final readonly class Grader
{
    public function __construct(private Score $score)
    {
        if ($this->score->value() < 0 || $this->score->value() > 100) {
            throw new InvalidArgumentException('Score must be between 0 and 100');
        }
    }

    public static function new(Score $score): self
    {
        return new self($score);
    }

    public function grade(): Grade
    {
        return match (true) {
            $this->score->value() >= 70 && $this->score->value() <= 100 => Grade::A,
            $this->score->value() >= 60 && $this->score->value() < 70 => Grade::B,
            $this->score->value() >= 50 && $this->score->value() < 60 => Grade::C,
            $this->score->value() >= 45 && $this->score->value() < 50 => Grade::D,
            $this->score->value() >= 40 && $this->score->value() < 45 => Grade::E,
            default => Grade::F,
        };
    }
}
