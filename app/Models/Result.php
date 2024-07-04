<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
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

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'score' => 'array',
        ];
    }
}
