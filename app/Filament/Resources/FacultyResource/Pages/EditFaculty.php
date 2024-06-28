<?php

declare(strict_types=1);

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    /** @return array<int, \Filament\Actions\DeleteAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
