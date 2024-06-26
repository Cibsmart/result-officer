<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListFaculties extends ListRecords
{

    protected static string $resource = FacultyResource::class;

    /** @return array<int, Actions\ViewAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
