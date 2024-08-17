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
        'remarks',
        'data',
    ];

    /** @var array<int, string> */
    protected $hidden = ['data'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CourseRegistration, \App\Models\Result> */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CourseRegistration::class);
    }

    public function getData(): string
    {
        return "{$this->course_registration_id}-{$this->total_score}-{$this->grade}-{$this->grade_point}";
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'score' => 'array',
            'upload_date' => 'date',
        ];
    }
}
