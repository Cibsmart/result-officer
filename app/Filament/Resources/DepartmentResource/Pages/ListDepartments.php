<?php

declare(strict_types=1);

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
