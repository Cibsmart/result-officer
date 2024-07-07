<?php

declare(strict_types=1);

namespace App\Classes;

final readonly class Score
{
    public function __construct(
        private int $course_work,
        private int $exam,
    ) {
    }

    public function total(): int
    {
        return $this->course_work + $this->exam;
    }
}
