<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Result extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'semester_enrollment_id',
        'scores',
        'total_score',
        'grade',
        'grade_point',
        'remark',
        'data',
    ];

    /** @var array<int, string> */
    protected $hidden = ['data'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course, \App\Models\Result> */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CourseRegistration::class);
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
