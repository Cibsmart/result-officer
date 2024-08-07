<?php

declare(strict_types=1);

namespace App\Data\Session;

use App\Models\Session;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SessionData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Session $session): self
    {
        return new self(id: $session->id, name: $session->name);
    }
}
