<?php

declare(strict_types=1);

namespace App\Values;

use Carbon\Carbon;
use Exception;

final class DateValue
{
    public function __construct(public ?Carbon $value)
    {
    }

    public static function fromValue(Carbon|string|null $date): self
    {
        if ($date === null || $date === '') {
            return new self(null);
        }

        if ($date instanceof Carbon) {
            return new self($date);
        }

        $carbonDate = null;

        try {
            $carbonDate = Carbon::parse($date);
        } catch (Exception) {
        }

        return new self($carbonDate);
    }

    public function toString(string $format = 'jS M, Y'): string
    {
        if ($this->value === null) {
            return '';
        }

        return $this->value->format($format);
    }
}
