<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class Course extends Model
{
    public static function createFromRawCourse(RawCourse $rawCourse): self
    {
        $course = new self();

        $course->code = $rawCourse->code;
        $course->title = $rawCourse->title;
        $course->online_id = $rawCourse->online_id;
        $course->slug = Str::slug("{$rawCourse->code}-{$rawCourse->title}");

        $course->save();

        return $course;
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
}
