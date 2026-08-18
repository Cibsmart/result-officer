<?php

declare(strict_types=1);

namespace App\ViewModels\Students;

use App\Data\Students\StudentFilterOptionsData;
use App\Data\Students\StudentIndexFilterData;
use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class StudentIndexPage extends Data
{
    public function __construct(
        /** @var \Illuminate\Pagination\AbstractPaginator<int, \App\Data\Students\StudentBasicData> $paginated */
        public readonly AbstractPaginator $paginated,
        public readonly StudentIndexFilterData $filters,
        public readonly StudentFilterOptionsData $options,
    ) {
    }
}
