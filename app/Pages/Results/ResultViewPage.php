<?php

namespace App\Pages\Results;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentData;
use Spatie\LaravelData\Data;

final class ResultViewPage extends Data
{
    public function __construct(
        public readonly StudentData $student,
        public StudentResultData $results
    ) {
    }
}
