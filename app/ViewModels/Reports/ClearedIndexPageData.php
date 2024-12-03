<?php

declare(strict_types=1);

namespace App\ViewModels\Reports;

use App\Data\Cleared\ClearedStudentListData;
use App\Data\Department\DepartmentListData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class ClearedIndexPageData extends Data
{
    public function __construct(
        #[TypeScriptType(DepartmentListData::class)]
        public readonly Closure $departments,
        #[TypeScriptType(ClearedStudentListData::class)]
        public readonly Closure $students,
    ) {
    }
}
