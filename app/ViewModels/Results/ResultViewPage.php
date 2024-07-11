<?php

declare(strict_types=1);

namespace App\ViewModels\Results;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentData;
use Spatie\LaravelData\Data;

final class ResultViewPage extends Data
{
    public function __construct(
        public readonly StudentData $student,
        public StudentResultData $results,
    ) {
    }
}
