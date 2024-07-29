<?php

declare(strict_types=1);

namespace App\ViewModels\Summary;

use App\Data\Summary\DepartmentResultSummaryData;
use Spatie\LaravelData\Data;

final class SummaryViewPage extends Data
{
    public function __construct(
        public readonly DepartmentResultSummaryData $department,
    ) {
    }
}
