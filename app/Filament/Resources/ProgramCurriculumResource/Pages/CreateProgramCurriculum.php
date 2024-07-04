<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\Pages;

use App\Filament\Resources\ProgramCurriculumResource;
use App\Models\ProgramCurriculum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

final class CreateProgramCurriculum extends CreateRecord
{
    protected static string $resource = ProgramCurriculumResource::class;

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     * @throws \Filament\Support\Exceptions\Halt
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $curriculum = ProgramCurriculum::query()
            ->where('program_id', $data['program_id'])
            ->where('curriculum_id', $data['curriculum_id'])
            ->where('level_id', $data['level_id'])
            ->where('semester_id', $data['semester_id'])
            ->first();

        if ($curriculum) {
            Notification::make()
                ->warning()
                ->title('Curriculum already exists!')
                ->send();

            $this->halt();
        }

        return $data;
    }
}
