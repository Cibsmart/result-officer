<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditFaculty extends EditRecord
{

    protected static string $resource = FacultyResource::class;

    /** @return array<int, Actions\ViewAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
