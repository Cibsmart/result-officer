<?php

declare(strict_types=1);

namespace App\Data\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ExcelImportEventListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Imports\ExcelImportEventData> */
        public readonly Collection $events,
    ) {
    }

    public static function forUser(User $user): self
    {
        $importEvents = $user->excelImportEvents()->limit(15)->get();

        return new self(events: ExcelImportEventData::collect($importEvents));
    }
}
