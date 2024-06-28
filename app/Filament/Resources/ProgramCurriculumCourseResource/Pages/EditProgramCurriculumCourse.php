<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumCourseResource\Pages;

use App\Filament\Resources\ProgramCurriculumCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditProgramCurriculumCourse extends EditRecord
{

    protected static string $resource = ProgramCurriculumCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
