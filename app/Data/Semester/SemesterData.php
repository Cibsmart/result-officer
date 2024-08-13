<?php

declare(strict_types=1);

namespace App\Data\Semester;

use App\Models\Semester;
use Spatie\LaravelData\Data;

final class SemesterData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Semester $semester): self
    {
        return new self(id: $semester->id, name: $semester->name);
    }
}
