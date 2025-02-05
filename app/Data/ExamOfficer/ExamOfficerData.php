<?php

declare(strict_types=1);

namespace App\Data\ExamOfficer;

use App\Models\ExamOfficer;
use Spatie\LaravelData\Data;

final class ExamOfficerData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(ExamOfficer $examOfficer): self
    {
        return new self(id: $examOfficer->id, name: $examOfficer->name);
    }
}
