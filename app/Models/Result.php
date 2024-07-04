<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Result extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'enrollment_id',
        'semester_id',
        'course_id',
        'credit_unit_id',
        'course_status_id',
        'course_work_score',
        'assessment_score',
        'final_exam_score',
        'scores',
        'total_exam_score',
        'grade',
        'grade_point',
        'remark',
        'data',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'data',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course, \App\Models\Result> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CourseStatus, \App\Models\Result> */
    public function courseStatus(): BelongsTo
    {
        return $this->belongsTo(CourseStatus::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CreditUnit, \App\Models\Result> */
    public function creditUnit(): BelongsTo
    {
        return $this->belongsTo(CreditUnit::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Enrollment, \App\Models\Result> */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, \App\Models\Result> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'score' => 'array',
        ];
    }
}
