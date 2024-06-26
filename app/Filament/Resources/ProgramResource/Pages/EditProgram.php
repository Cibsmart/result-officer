<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditProgram extends EditRecord
{

    protected static string $resource = ProgramResource::class;

    /** @return array<int, \Filament\Actions\DeleteAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
