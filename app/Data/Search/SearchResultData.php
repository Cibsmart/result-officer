<?php

declare(strict_types=1);

namespace App\Data\Search;

use Spatie\LaravelData\Data;

final class SearchResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $subtitle,
        public readonly string $url,
        public readonly ?string $badge,
        public readonly bool $external,
    ) {
    }

    public static function make(
        int $id,
        string $title,
        string $url,
        ?string $subtitle = null,
        ?string $badge = null,
        bool $external = false,
    ): self {
        return new self(id: $id, title: $title, subtitle: $subtitle, url: $url, badge: $badge, external: $external);
    }
}
