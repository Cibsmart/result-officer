<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumLevelResource\Pages;

use App\Filament\Resources\ProgramCurriculumLevelResource;
use Filament\Resources\Pages\ListRecords;

final class ListProgramCurriculumLevels extends ListRecords
{
    protected static string $resource = ProgramCurriculumLevelResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
