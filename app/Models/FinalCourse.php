<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\CourseCode;
use App\Values\CourseTitle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class FinalCourse extends Model
{
    public static function getOrCreateFromCourse(Course $course): self
    {
        $finalCourse = self::getUsingSlug($course->slug);

        return $finalCourse
            ? $finalCourse
            : self::createFinalCourse($course->code, $course->title);
    }

    public static function getOrCreateUsingCodeAndTitle(CourseCode $code, CourseTitle $title): self
    {
        $slug = Str::slug("{$code->value}-{$title->value}");

        $finalCourse = self::getUsingSlug($slug);

        return $finalCourse
            ? $finalCourse
            : self::createFinalCourse($code->value, $title->value);
    }

    public function checkForDuplicateInFinalSemesterEnrollment(FinalSemesterEnrollment $semesterEnrollment): bool
    {
        $courses = self::query()
            ->whereIn('id', $semesterEnrollment->finalResults()->pluck('final_course_id'))
            ->get();

        return $courses->contains('id', $this->id) || $courses->contains('code', $this->code);
    }

    private static function getUsingSlug(string $slug): ?self
    {
        return
            Cache::remember("final_course_slug.{$slug}",
                fn (?self $value) => is_null($value) ? 0 : now()->addDay(),
                fn () => self::where('slug', $slug)->first(),
            );
    }

    private static function createFinalCourse(string $courseCode, string $courseTitle): self
    {
        $finalCourse = new self();

        $finalCourse->code = $courseCode;
        $finalCourse->title = $courseTitle;
        $finalCourse->slug = Str::slug("{$courseCode}-{$courseTitle}");

        $finalCourse->save();

        return $finalCourse;
    }
}
