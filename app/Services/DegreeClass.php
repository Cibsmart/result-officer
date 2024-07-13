<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DegreeClassEnum;

final readonly class DegreeClass
{
    public function __construct(private float $fcgpa)
    {
    }

    public static function for(float $fcgpa): self
    {
        return new self($fcgpa);
    }

    public function value(): DegreeClassEnum
    {
        return match (true) {
            $this->fcgpa >= 4.50 && $this->fcgpa <= 5.00 => DegreeClassEnum::FIRST_CLASS,
            $this->fcgpa >= 3.50 && $this->fcgpa < 4.50 => DegreeClassEnum::SECOND_CLASS_UPPER,
            $this->fcgpa >= 2.50 && $this->fcgpa < 3.50 => DegreeClassEnum::SECOND_CLASS_LOWER,
            $this->fcgpa >= 1.50 && $this->fcgpa < 2.50 => DegreeClassEnum::THIRD_CLASS,
            $this->fcgpa >= 1.00 && $this->fcgpa < 1.50 => DegreeClassEnum::PASS,
            default => DegreeClassEnum::FAIL,
        };
    }
}
