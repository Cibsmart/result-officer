<?php

declare(strict_types=1);

namespace App\ViewModels\Students;

use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class StudentIndexPage extends Data
{
    public function __construct(
        /** @var \Illuminate\Pagination\AbstractPaginator<\App\Data\Students\StudentBasicData> $paginated */
        public readonly AbstractPaginator $paginated,
    ) {
    }
}
