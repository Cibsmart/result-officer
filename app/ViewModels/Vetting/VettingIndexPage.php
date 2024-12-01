<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Department\DepartmentListData;
use App\Data\Vetting\VettingListData;
use App\Data\Vetting\VettingStepListData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class VettingIndexPage extends Data
{
    public function __construct(
        #[TypeScriptType(DepartmentListData::class)]
        public readonly Closure $departments,
        #[TypeScriptType(VettingListData::class)]
        public readonly Closure $data,
        #[TypeScriptType(VettingStepListData::class)]
        public readonly Closure $steps,
    ) {
    }
}
