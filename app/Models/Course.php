<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\CourseCode;
use App\Values\CourseTitle;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class Course extends Model
{
    public static function createFromRawCourse(RawCourse $rawCourse): self
    {
        $code = CourseCode::new($rawCourse->code);
        $title = CourseTitle::new($rawCourse->title);

        return self::createCourse($code->value, $title->value, $rawCourse->online_id);
    }

    public static function getUsingOnlineId(string $onlineId): self
    {
        return
            Cache::remember("course_online_id_{$onlineId}",
                now()->addDay(),
                fn () => self::query()->where('online_id', $onlineId)->firstOrFail());
    }

    public static function getUsingCode(string $courseCode): self
    {
        return
            Cache::remember($courseCode,
                now()->addDay(),
                fn () => self::query()->where('code', $courseCode)->firstOrFail());
    }

    public static function getUsingLegacyCourseId(int $legacyCourseId): self
    {
        $alternative = LegacyCourseAlternatives::query()->where('original_course_id', $legacyCourseId)->firstOrFail();

        return self::query()->where('id', $alternative->alternative_course_id)->firstOrFail();
    }

    public static function getOrCreateUsingCodeAndTitle(CourseCode $code, CourseTitle $title): self
    {
        $courses = self::query()->where('code', $code->value)->get();

        $exactMatch = self::checkExactmatchingCourse($courses, $title->value);

        if ($exactMatch) {
            return $exactMatch;
        }

        $bestMatch = self::getBestMatchingCourse($courses, $title->value);

        if ($bestMatch) {
            return $bestMatch;
        }

        return self::createCourse($code->value, $title->value);
    }

    public function checkForDuplicateInCurriculumSemester(ProgramCurriculumSemester $curriculumSemester): bool
    {
        $courses = self::query()
            ->whereIn('id', $curriculumSemester->programCurriculumCourses()->pluck('course_id'))
            ->get();

        return $courses->contains('id', $this->id) || $courses->contains('code', $this->code);
    }

    public function checkForDuplicateInSemesterEnrollment(SemesterEnrollment $semesterEnrollment): bool
    {
        $courses = self::query()
            ->whereIn('id', $semesterEnrollment->registrations()->pluck('course_id'))
            ->get();

        return $courses->contains('id', $this->id) || $courses->contains('code', $this->code);
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter */
            get: fn (
                ?string $value,
                array $attributes,
            ): string => "({$attributes['code']}) {$attributes['title']}",
        );
    }

    private static function createCourse(
        string $courseCode,
        string $courseTitle,
        ?string $onlineId = null,
    ): self {
        $course = new self();

        $course->code = $courseCode;
        $course->title = $courseTitle;
        $course->online_id = $onlineId;
        $course->slug = Str::slug("{$courseCode}-{$courseTitle}");

        $course->save();

        return $course;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses */
    private static function checkExactMatchingCourse(Collection $courses, string $title): ?self
    {
        foreach ($courses as $course) {
            if ($course->title === $title) {
                return $course;
            }
        }

        return null;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses */
    private static function getBestMatchingCourse(Collection $courses, string $title): ?self
    {
        $bestMatch = null;
        $bestMatchScore = 0.0;

        foreach ($courses as $course) {
            similar_text($title, $course->title, $percent);

            if ($percent <= $bestMatchScore) {
                continue;
            }

            $bestMatchScore = $percent;
            $bestMatch = $course;
        }

        return $bestMatchScore >= 80.0
            ? $bestMatch
            : null;
    }
}
