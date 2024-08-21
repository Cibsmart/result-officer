<?php

declare(strict_types=1);

namespace App\Data\Download;

use Carbon\Carbon;
use Exception;
use Spatie\LaravelData\Data;

final class PortalDateData extends Data
{
    public function __construct(
        public readonly ?Carbon $value,
    ) {
    }

    public static function fromArray(string $date): self
    {
        $carbonDate = null;

        try {
            $carbonDate = $date !== ''
                ? Carbon::parse($date)
                : $carbonDate;
        } catch (Exception) {
        }

        return new self($carbonDate);
    }

    public function getStringDate(string $format = 'Y-m-d'): ?string
    {
        return $this->value
            ? $this->value->format($format)
            : $this->value;
    }
}
