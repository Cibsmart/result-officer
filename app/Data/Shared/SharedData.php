<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class SharedData extends Data
{
    public function __construct(
        #[TypeScriptType(UserData::class)]
        public ?Closure $user = null,
    ) {
    }
}
