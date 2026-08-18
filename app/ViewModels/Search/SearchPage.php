<?php

declare(strict_types=1);

namespace App\ViewModels\Search;

use Spatie\LaravelData\Data;

final class SearchPage extends Data
{
    public function __construct(
        public readonly string $query,
        /** @var array<int, \App\Data\Search\SearchGroupData> $groups */
        public readonly array $groups,
    ) {
    }
}
