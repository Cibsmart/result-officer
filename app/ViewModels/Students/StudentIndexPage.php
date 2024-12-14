<?php

declare(strict_types=1);

namespace App\ViewModels\Students;

use Illuminate\Contracts\Pagination\Paginator;
use Spatie\LaravelData\Data;

final class StudentIndexPage extends Data
{
    public function __construct(
        /** @var \Illuminate\Contracts\Pagination\Paginator<\App\Data\Students\StudentBasicData> $paginated */
        public readonly Paginator $paginated,
    ) {
    }
}
