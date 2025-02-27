<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class VettingIndexPage extends Data
{
    /** @param \Illuminate\Pagination\AbstractPaginator<\App\Data\Vetting\VettingEventGroupData> $paginated */
    public function __construct(
        public readonly AbstractPaginator $paginated,
    ) {
    }
}
