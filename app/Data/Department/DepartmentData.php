<?php

declare(strict_types=1);

namespace App\Data\Department;

use App\Models\Department;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class DepartmentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Department $department): self
    {
        return new self(id: $department->id, name: $department->name);
    }
}
