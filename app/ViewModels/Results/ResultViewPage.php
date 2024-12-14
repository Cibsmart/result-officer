<?php

declare(strict_types=1);

namespace App\ViewModels\Results;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentBasicData;
use Spatie\LaravelData\Data;

final class ResultViewPage extends Data
{
    public function __construct(
        public readonly StudentBasicData $student,
        public readonly StudentResultData $results,
    ) {
    }
}
