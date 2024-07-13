<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DegreeClass;

final readonly class ClassOfDegree
{
    public function __construct(private float $fcgpa)
    {
    }

    public static function for(float $fcgpa): self
    {
        return new self($fcgpa);
    }

    public function value(): DegreeClass
    {
        return match (true) {
            $this->fcgpa >= 4.50 && $this->fcgpa <= 5.00 => DegreeClass::FIRST_CLASS,
            $this->fcgpa >= 3.50 && $this->fcgpa < 4.50 => DegreeClass::SECOND_CLASS_UPPER,
            $this->fcgpa >= 2.50 && $this->fcgpa < 3.50 => DegreeClass::SECOND_CLASS_LOWER,
            $this->fcgpa >= 1.50 && $this->fcgpa < 2.50 => DegreeClass::THIRD_CLASS,
            $this->fcgpa >= 1.00 && $this->fcgpa < 1.50 => DegreeClass::PASS,
            default => DegreeClass::FAIL,
        };
    }
}
