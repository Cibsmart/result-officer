<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumCourseResource\Pages;

use App\Filament\Resources\ProgramCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditProgramCurriculumCourse extends EditRecord
{
    protected static string $resource = ProgramCourseResource::class;

    /** @return array<int, \Filament\Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
