<?php

declare(strict_types=1);

namespace App\Data\Level;

use App\Models\Level;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class LevelData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Level $level): self
    {
        return new self(id: $level->id, name: $level->name);
    }
}
