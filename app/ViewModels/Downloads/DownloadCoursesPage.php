<?php

declare(strict_types=1);

namespace App\ViewModels\Downloads;

use App\Data\Imports\PendingImportEventData;
use Closure;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class DownloadCoursesPage extends Data
{
    public function __construct(
        #[TypeScriptType(Collection::class)]
        public readonly Closure $events,
        #[TypeScriptType(PendingImportEventData::class)]
        public readonly Closure $pending,
    ) {
    }
}
