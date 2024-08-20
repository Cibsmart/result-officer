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

    public static function new(string $search = ''): self
    {
        $default = new CourseData(id: 0, name: 'Type and Select course ...');

        $courses = Course::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereAny(['title', 'code'], 'like', "$search%");
            })
            ->select(['id', 'code', 'title'])
            ->orderBy('code')
            ->limit(200)
            ->get();

        return new self(courses: CourseData::collect($courses)->prepend($default));
    }
}
