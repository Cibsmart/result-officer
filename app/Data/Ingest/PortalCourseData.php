<?php

declare(strict_types=1);

namespace App\Data\Ingest;

use Spatie\LaravelData\Data;

final class PortalCourseData extends Data
{
    public function __construct(
        public readonly string $onlineId,
        public readonly string $code,
        public readonly string $title,
    ) {
    }

    /** @param array{id: string, course_code: string, course_title: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(onlineId: (string) $data['id'], code: $data['course_code'], title: $data['course_code']);
    }
}
