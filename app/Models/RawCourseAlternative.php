<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class RawCourseAlternative extends Model
{
    public static function getAlternativeOnlineCourseId(string $originalOnlineCourseId): ?self
    {
        return
            Cache::remember("raw_course_alternative_{$originalOnlineCourseId}",
                fn (?self $value) => is_null($value) ? null : now()->addHour(),
                fn () => self::query()->firstWhere('original_course_id', $originalOnlineCourseId));
    }
}
