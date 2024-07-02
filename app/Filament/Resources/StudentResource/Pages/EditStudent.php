<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    /** @return array<int, \Filament\Actions\DeleteAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
