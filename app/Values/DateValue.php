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

    public static function fromString(?string $date): self
    {
        $carbonDate = null;

        try {
            $carbonDate = $date !== '' && $date !== null
                ? Carbon::parse($date)
                : $carbonDate;
        } catch (Exception) {
        }

        return new self($carbonDate);
    }
}
