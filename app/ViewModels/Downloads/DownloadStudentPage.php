<?php

declare(strict_types=1);

namespace App\ViewModels\Downloads;

use App\Data\Department\DepartmentListData;
use App\Data\Import\PendingImportEventData;
use App\Data\Session\SessionListData;
use Closure;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class DownloadStudentPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $department,
        public readonly SessionListData $session,
        #[TypeScriptType(Collection::class)]
        public readonly Closure $events,
        #[TypeScriptType(PendingImportEventData::class)]
        public readonly Closure $pending,
        public readonly int $selectedIndex,
    ) {
    }
}
