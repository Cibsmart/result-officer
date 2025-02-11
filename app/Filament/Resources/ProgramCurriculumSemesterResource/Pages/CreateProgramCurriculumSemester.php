<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumSemesterResource\Pages;

use App\Filament\Resources\ProgramCurriculumSemesterResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateProgramCurriculumSemester extends CreateRecord
{
    protected static string $resource = ProgramCurriculumSemesterResource::class;
}
