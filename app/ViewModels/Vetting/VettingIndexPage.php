<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Department\DepartmentListData;
use App\Data\Vetting\PaginatedVettingListData;
use App\Data\Vetting\VettingStepListData;
use App\ViewModels\Clearance\ClearanceFormPage;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class VettingIndexPage extends Data
{
    public function __construct(
        #[TypeScriptType(DepartmentListData::class)]
        public readonly Closure $departments,
        #[TypeScriptType(ClearanceFormPage::class)]
        public readonly Closure $clearance,
        #[TypeScriptType(VettingStepListData::class)]
        public readonly Closure $steps,
        #[TypeScriptType(DepartmentInfoData::class)]
        public readonly Closure $department,
        #[TypeScriptType(PaginatedVettingListData::class)]
        public readonly Closure $data,
    ) {
    }
}
