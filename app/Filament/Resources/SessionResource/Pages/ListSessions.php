<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SessionResource\Pages;

use App\Filament\Resources\SessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListSessions extends ListRecords
{

    protected static string $resource = SessionResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
