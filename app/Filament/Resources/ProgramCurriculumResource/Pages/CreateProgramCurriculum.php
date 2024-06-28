<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\Pages;

use App\Filament\Resources\ProgramCurriculumResource;
use App\Models\Curriculum;
use App\Models\Level;
use App\Models\Program;
use App\Models\ProgramCurriculum;
use App\Models\Semester;
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
        $programCode = Program::query()->findOrFail($data['program_id'])->code;
        $curriculumCode = Curriculum::query()->findOrFail($data['curriculum_id'])->code;
        $levelName = Level::query()->findOrFail($data['level_id'])->name;
        $semesterName = Semester::query()->findOrFail($data['semester_id'])->name;

        $slug = "$programCode-$curriculumCode-$levelName-$semesterName";

        $curriculum = ProgramCurriculum::query()->where('slug', $slug)->first();

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
