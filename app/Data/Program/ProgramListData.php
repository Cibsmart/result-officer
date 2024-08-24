<?php

declare(strict_types=1);

namespace App\Data\Program;

use App\Models\Program;
use App\Scopes\ActiveScope;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ProgramListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Program\ProgramData> */
        public readonly Collection $programs,
    ) {
    }

    public static function new(): self
    {
        $default = new ProgramData(id: 0, name: 'Select Department');

        return new self(
            programs: ProgramData::collect(
                Program::query()
                    ->tap(new ActiveScope())
                    ->orderBy('name')
                    ->get(),
            )->prepend($default),
        );
    }
}
