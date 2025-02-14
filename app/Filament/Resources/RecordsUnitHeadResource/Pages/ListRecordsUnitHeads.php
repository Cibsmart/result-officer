<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecordsUnitHeadResource\Pages;

use App\Filament\Resources\RecordsUnitHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListRecordsUnitHeads extends ListRecords
{
    protected static string $resource = RecordsUnitHeadResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
