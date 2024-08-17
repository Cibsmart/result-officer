<?php

declare(strict_types=1);

namespace App\Models;

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
        'course_status',
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Result> */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    public function student(): Student
    {
        return $this->semesterEnrollment->enrollment->student;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
        ];
    }
}
