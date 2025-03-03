<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Vetting\VettingEventGroupData;
use Closure;
use Inertia\DeferProp;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class VettingShowPage extends Data
{
    public function __construct(
        #[TypeScriptType(VettingEventGroupData::class)]
        public readonly Closure $event,
        public readonly DeferProp $data,
    ) {
    }
}
