<?php

namespace App\Filament\Resources\ProgramCurriculumSemesterResource\Pages;

use App\Filament\Resources\ProgramCurriculumSemesterResource;
use Filament\Resources\Pages\EditRecord;

class EditProgramCurriculumSemester extends EditRecord
{
    protected static string $resource = ProgramCurriculumSemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
