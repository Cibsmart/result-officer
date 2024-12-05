<?php

declare(strict_types=1);

namespace App\Data\Semester;

use App\Models\Semester;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SemesterListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Semester\SemesterData> */
        public readonly Collection $semesters,
    ) {
    }

    public static function new(): self
    {
        $default = new SemesterData(id: 0, name: 'Select Semester', slug: '');

        return new self(
            semesters: SemesterData::collect(
                Semester::query()->orderBy('name')->get(),
            )->prepend($default),
        );
    }
}
