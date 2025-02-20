<?php

declare(strict_types=1);

namespace App\Data\Enums;

use App\Enums\StudentStatus;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StudentStatusListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Enums\StudentStatusData> */
        public readonly Collection $data,
    ) {
    }

    public static function new(): self
    {
        $default = new StudentStatusData(id: '0', name: 'Select Status');

        return new self(data: StudentStatusData::collect(collect(StudentStatus::cases()))->prepend($default));
    }
}
