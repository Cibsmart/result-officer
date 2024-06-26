<?php

namespace App\Filament\Resources\SessionResource\Pages;

use App\Filament\Resources\SessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditSession extends EditRecord
{

    protected static string $resource = SessionResource::class;

    /** @return array<int, \Filament\Actions\ViewAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
