<?php

declare(strict_types=1);

namespace App\Data\ExamOfficer;

use App\Models\ExamOfficer;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ExamOfficerListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\ExamOfficer\ExamOfficerData> */
        public readonly Collection $data,
    ) {
    }

    public static function new(): self
    {
        $default = new ExamOfficerData(id: 0, name: 'Select Exam Officer');

        return new self(
            data: ExamOfficerData::collect(
                ExamOfficer::query()->orderBy('name')->get(),
            )->prepend($default),
        );
    }
}
