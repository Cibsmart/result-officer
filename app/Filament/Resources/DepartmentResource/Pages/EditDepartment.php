<?php

declare(strict_types=1);

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    /** @return array<int, \Filament\Actions\DeleteAction> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
