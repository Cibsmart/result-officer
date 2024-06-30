<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\Pages;

use App\Filament\Resources\ProgramCurriculumResource;
use Filament\Resources\Pages\EditRecord;

final class EditProgramCurriculum extends EditRecord
{
    protected static string $resource = ProgramCurriculumResource::class;

    /** @return array<int, \Filament\Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
