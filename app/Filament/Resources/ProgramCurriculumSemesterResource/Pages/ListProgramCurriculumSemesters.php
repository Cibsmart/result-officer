<?php

namespace App\Filament\Resources\ProgramCurriculumSemesterResource\Pages;

use App\Filament\Resources\ProgramCurriculumSemesterResource;
use Filament\Resources\Pages\ListRecords;

class ListProgramCurriculumSemesters extends ListRecords
{
    protected static string $resource = ProgramCurriculumSemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
