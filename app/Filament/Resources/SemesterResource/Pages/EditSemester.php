<?php

declare(strict_types=1);

namespace App\Filament\Resources\SemesterResource\Pages;

use App\Filament\Resources\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditSemester extends EditRecord
{
    protected static string $resource = SemesterResource::class;

    /** @return array<int, \Filament\Actions\DeleteAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
