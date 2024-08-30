<?php

declare(strict_types=1);

namespace App\ViewModels\Reports;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class CompositeViewPage extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData> */
        public readonly Collection $students,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseListData> */
        public readonly Collection $courses,
    ) {
    }
}
