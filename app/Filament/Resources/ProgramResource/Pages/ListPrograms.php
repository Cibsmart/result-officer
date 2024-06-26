<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListPrograms extends ListRecords
{

    protected static string $resource = ProgramResource::class;

    /** @return array<int, Actions\ViewAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
