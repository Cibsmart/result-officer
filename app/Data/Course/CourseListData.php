<?php

declare(strict_types=1);

namespace App\Data\Course;

use App\Models\Course;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class CourseListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Course\CourseData> */
        public readonly Collection $courses,
    ) {
    }

    public static function new(): self
    {
        $default = new CourseData(id: 0, name: 'Select Course');

        return new self(
            courses: CourseData::collect(
                Course::query()->orderBy('code')->get(),
            )->prepend($default),
        );
    }
}
