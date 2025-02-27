<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class VettingIndexPage extends Data
{
    public function __construct(
        /** @param \Illuminate\Pagination\AbstractPaginator<\App\Data\Vetting\VettingEventGroupData> $paginated */
        public readonly AbstractPaginator $paginated,
    ) {
    }
}
