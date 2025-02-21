<?php

declare(strict_types=1);

namespace App\Data\Enums;

use App\Enums\CreditUnit;
use Spatie\LaravelData\Data;

final class CreditUnitData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromEnum(CreditUnit $creditUnit): self
    {
        return new self(id: $creditUnit->value, name: $creditUnit->name);
    }
}
