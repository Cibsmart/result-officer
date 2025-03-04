<?php

declare(strict_types=1);

namespace App\Data\Curriculum;

use App\Models\ProgramCurriculum;
use Spatie\LaravelData\Data;

final class ProgramCurriculumData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $program,
        public readonly string $curriculum,
        public readonly string $entrySession,
        public readonly string $entryMode,
    ) {
    }

    public static function fromModel(ProgramCurriculum $programCurriculum): self
    {
        $program = $programCurriculum->program;
        $session = $programCurriculum->session;
        $curriculum = $programCurriculum->curriculum;

        $name = "{$curriculum->code} - {$program->name} - {$programCurriculum->entry_mode} - {$session->name}";

        return new self(
            id: $programCurriculum->id,
            name: $name,
            program: $program->name,
            curriculum: $curriculum->code,
            entrySession: $session->name,
            entryMode: $programCurriculum->entry_mode,
        );
    }

    public static function getEmpty(): self
    {
        $name = 'Course List Not Found';

        return new self(id: 0, name: $name, program: '', curriculum: '', entrySession: '', entryMode: '');
    }
}
