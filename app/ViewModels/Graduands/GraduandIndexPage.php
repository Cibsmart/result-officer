<?php

declare(strict_types=1);

namespace App\ViewModels\Graduands;

use App\Data\Department\DepartmentInfoData;
use App\Data\Graduands\PaginatedGraduandListData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class GraduandIndexPage extends Data
{
    public function __construct(
        #[TypeScriptType(DepartmentInfoData::class)]
        public readonly Closure $department,
        #[TypeScriptType(PaginatedGraduandListData::class)]
        public readonly Closure $data,
    ) {
    }
}
