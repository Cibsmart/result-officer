<?php

declare(strict_types=1);

namespace App\ViewModels\Results;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentBasicData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class ResultViewPage extends Data
{
    public function __construct(
        #[TypeScriptType(StudentBasicData::class)]
        public readonly Closure $student,
        #[TypeScriptType(StudentResultData::class)]
        public readonly Closure $results,
    ) {
    }
}
