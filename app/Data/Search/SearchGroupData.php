<?php

declare(strict_types=1);

namespace App\Data\Search;

use App\Enums\SearchGroup;
use Spatie\LaravelData\Data;

final class SearchGroupData extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        /** @var array<int, \App\Data\Search\SearchResultData> $results */
        public readonly array $results,
    ) {
    }

    /** @param array<int, \App\Data\Search\SearchResultData> $results */
    public static function fromGroup(SearchGroup $group, array $results): self
    {
        return new self(key: $group->value, label: $group->label(), results: $results);
    }
}
