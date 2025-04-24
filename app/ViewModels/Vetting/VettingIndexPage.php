<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class VettingIndexPage extends Data
{
    public function __construct(
        /** @var \Illuminate\Pagination\AbstractPaginator<int, \App\Data\Vetting\VettingEventGroupData> */
        public readonly AbstractPaginator $paginated,
    ) {
    }
}
