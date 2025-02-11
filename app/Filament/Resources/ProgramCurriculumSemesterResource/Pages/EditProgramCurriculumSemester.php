<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumSemesterResource\Pages;

use App\Filament\Resources\ProgramCurriculumSemesterResource;
use Filament\Resources\Pages\EditRecord;

final class EditProgramCurriculumSemester extends EditRecord
{
    protected static string $resource = ProgramCurriculumSemesterResource::class;

    /** @return array<int, \Filament\Actions\CreateAction> */
    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
