<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RawCourseAlternative extends Model
{
    public static function getAlternativeOnlineCourseId(string $originalOnlineCourseId): ?self
    {
        return self::query()->firstWhere('original_course_id', $originalOnlineCourseId);
    }
}
