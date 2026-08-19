<?php

declare(strict_types=1);

namespace App\Enums;

enum ClassOfDegree: string
{
    case FIRST_CLASS = 'FIRST CLASS HONOURS';
    case SECOND_CLASS_UPPER = 'SECOND CLASS HONOURS (UPPER DIVISION)';
    case SECOND_CLASS_LOWER = 'SECOND CLASS HONOURS (LOWER DIVISION)';
    case THIRD_CLASS = 'THIRD CLASS HONOURS';
    case PASS = 'PASS';
    case FAIL = 'FAIL';

    /** The highest attainable FCGPA. Anything above it is corrupt data, not a first class. */
    private const float MAXIMUM_FCGPA = 5.00;

    /**
     * Classification is keyed on lower bounds alone, so the bands are contiguous.
     *
     * The previous implementation matched closed ranges — 4.50-5.00, 3.50-4.49,
     * 2.50-3.49 and so on — leaving a 0.01-wide gap above each band's top. A
     * value inside a gap matched no case and fell through to FAIL. The live path
     * rounds to two decimals and could never land in one, but the final path
     * rounds to three, so an FCGPA of 4.495 read as SECOND CLASS UPPER before
     * clearance and FAIL after it. The same held at 3.495, 2.495 and 1.495.
     */
    public static function for(float $fcgpa): self
    {
        if ($fcgpa < self::FAIL->min() || $fcgpa > self::MAXIMUM_FCGPA) {
            return self::FAIL;
        }

        return match (true) {
            $fcgpa >= self::FIRST_CLASS->min() => self::FIRST_CLASS,
            $fcgpa >= self::SECOND_CLASS_UPPER->min() => self::SECOND_CLASS_UPPER,
            $fcgpa >= self::SECOND_CLASS_LOWER->min() => self::SECOND_CLASS_LOWER,
            $fcgpa >= self::THIRD_CLASS->min() => self::THIRD_CLASS,
            $fcgpa >= self::PASS->min() => self::PASS,
            default => self::FAIL,
        };
    }

    /** Inclusive lower bound of the band. The upper bound is the next band's minimum. */
    public function min(): float
    {
        return match ($this) {
            self::FIRST_CLASS => 4.50,
            self::SECOND_CLASS_UPPER => 3.50,
            self::SECOND_CLASS_LOWER => 2.50,
            self::THIRD_CLASS => 1.50,
            self::PASS => 1.00,
            self::FAIL => 0.00,
        };
    }
}
