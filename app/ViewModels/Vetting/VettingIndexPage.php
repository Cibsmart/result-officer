<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Vetting\PaginatedVettingListData;
use App\Data\Vetting\VettingStepListData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class VettingIndexPage extends Data
{
    public function __construct(
        #[TypeScriptType(VettingStepListData::class)]
        public readonly Closure $steps,
        #[TypeScriptType(DepartmentInfoData::class)]
        public readonly Closure $department,
        #[TypeScriptType(PaginatedVettingListData::class)]
        public readonly Closure $data,
    ) {
    }
}
