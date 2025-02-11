<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\Pages;

use App\Filament\Resources\ProgramCurriculumResource;
use Filament\Resources\Pages\ListRecords;

final class ListProgramCurricula extends ListRecords
{
    protected static string $resource = ProgramCurriculumResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
