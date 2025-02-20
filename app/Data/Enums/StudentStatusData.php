<?php

declare(strict_types=1);

namespace App\Data\Enums;

use App\Enums\StudentStatus;
use Spatie\LaravelData\Data;

final class StudentStatusData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public static function fromEnum(StudentStatus $studentStatus): self
    {
        return new self(id: $studentStatus->value, name: $studentStatus->name);
    }
}
