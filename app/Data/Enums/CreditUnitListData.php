<?php

declare(strict_types=1);

namespace App\Data\Enums;

use App\Enums\StudentStatus;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class CreditUnitListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Enums\StudentStatusData> */
        public readonly Collection $data,
    ) {
    }

    public static function new(): self
    {
        $default = new CreditUnitData(id: 0, name: 'Select Credit Unit');

        return new self(data: CreditUnitData::collect(collect(StudentStatus::cases()))->prepend($default));
    }
}
