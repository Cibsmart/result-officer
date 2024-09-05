<?php

declare(strict_types=1);

namespace App\Data\Faculty;

use App\Models\Faculty;
use App\Models\Program;
use Spatie\LaravelData\Data;

final class FacultyData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Faculty $faulty): self
    {
        return new self(id: $faulty->id, name: $faulty->name);
    }

    public static function fromProgram(Program $program): self
    {
        $faulty = $program->department->faculty;

        return new self(id: $faulty->id, name: $faulty->name);
    }
}
