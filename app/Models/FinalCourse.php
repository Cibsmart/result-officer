<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FinalCourse extends Model
{
    public static function getOrCreateFromCourse(Course $course): self
    {
        $finalCourse = self::where('slug', $course->slug)->first();

        if ($finalCourse !== null) {
            return $finalCourse;
        }

        $finalCourse = new self();

        $finalCourse->code = $course->code;
        $finalCourse->title = $course->title;
        $finalCourse->slug = $course->slug;

        $finalCourse->save();

        return $finalCourse;
    }
}
