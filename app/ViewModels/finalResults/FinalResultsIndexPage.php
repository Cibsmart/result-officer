<?php

declare(strict_types=1);

namespace App\ViewModels\finalResults;

use App\Data\FinalResults\FinalStudentResultData;
use App\Data\Students\StudentBasicData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class FinalResultsIndexPage extends Data
{
    public function __construct(
        #[TypeScriptType(StudentBasicData::class)]
        public readonly Closure $student,
        #[TypeScriptType(FinalStudentResultData::class)]
        public readonly Closure $results,
    ) {
    }
}
