<?php

declare(strict_types=1);

namespace App\Actions\Imports\Courses;

use App\Enums\RawDataStatus;
use App\Models\Course;
use App\Models\RawCourse;
use App\Models\RawCourseAlternative;
use Illuminate\Database\Eloquent\Builder;

final class ProcessPortalCourse
{
    public function execute(RawCourse $rawCourse): void
    {
        $alternativeOnlineCourseId = RawCourseAlternative::getAlternativeOnlineCourseId($rawCourse->online_id);

        $onlineCourseId = $alternativeOnlineCourseId === null
            ? $rawCourse->online_id
            : $alternativeOnlineCourseId->alternative_course_id;

        $exists = Course::query()
            ->where('code', $rawCourse->code)
            ->where('title', $rawCourse->title)
            ->orWhere(fn (Builder $query) => $query->where('online_id', $onlineCourseId))
            ->exists();

        if ($exists) {
            $rawCourse->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        $course = Course::createFromRawCourse($rawCourse);

        $rawCourse->updateStatusAndCourse(RawDataStatus::PROCESSED, $course);
    }
}
