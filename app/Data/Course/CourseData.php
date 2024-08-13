<?php

declare(strict_types=1);

namespace App\Data\Course;

use App\Models\Course;
use Spatie\LaravelData\Data;

final class CourseData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $name,
    ) {
    }

    public static function fromModel(Course $course): self
    {
        return new self(id: $course->id, code: $course->code, name: $course->title);
    }
}
