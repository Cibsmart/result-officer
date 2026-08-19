<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class CourseAlternative extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function getUsingOriginalCourseId(int $courseId): Collection
    {
        return self::query()
            ->whereNull('program_curriculum_course_id')
            ->where('original_course_id', $courseId)
            ->get();
    }
}
