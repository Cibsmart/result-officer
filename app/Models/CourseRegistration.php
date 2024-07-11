<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreditUnitEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class CourseRegistration extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'semester_enrollment_id',
        'course_id',
        'credit_unit',
        'course_status_id',
    ];

    protected $with = ['result', 'course'];

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SemesterEnrollment, \App\Models\CourseRegistration>
     */
    public function semesterEnrollment(): BelongsTo
    {
        return $this->belongsTo(SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course, \App\Models\CourseRegistration> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CourseStatus, \App\Models\CourseRegistration>
     */
    public function courseStatus(): BelongsTo
    {
        return $this->belongsTo(CourseStatus::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Result> */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'credit_unit' => CreditUnitEnum::class,
        ];
    }

}
