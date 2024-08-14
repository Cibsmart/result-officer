<?php

declare(strict_types=1);

namespace App\Data\Download;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

final class PortalDateData extends Data
{
    public function __construct(
        public readonly string $day,
        public readonly string $month,
        public readonly string $year,
    ) {
    }

    /** @param array{day: string, month: string, year: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(day: $data['day'], month: $data['month'], year: $data['year']);
    }

    public function getCarbonDate(): ?Carbon
    {
        if ($this->day === '' || $this->month === '' || $this->year === '') {
            return null;
        }

        return Carbon::createFromDate((int) $this->year, (int) $this->month, (int) $this->day);
    }

    public function getStringDate(string $format = 'Y-m-d'): ?string
    {
        $date = $this->getCarbonDate();

        return $date
            ? $date->format($format)
            : $date;
    }
}
