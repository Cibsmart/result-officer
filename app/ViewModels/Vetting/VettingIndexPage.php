<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Department\DepartmentListData;
use App\Data\Vetting\VettingListData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class VettingIndexPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $departments,
        #[TypeScriptType(VettingListData::class)]
        public readonly Closure $data,
    ) {
    }
}
