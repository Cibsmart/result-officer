<?php

declare(strict_types=1);

namespace App\Data\Program;

use App\Models\Program;
use Spatie\LaravelData\Data;

final class ProgramData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
    ) {
    }

    public static function fromModel(Program $program): self
    {
        return new self(id: $program->id, name: $program->name, slug: $program->slug);
    }
}
