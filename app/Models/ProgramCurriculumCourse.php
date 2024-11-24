<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProgramCurriculumCourse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_curriculum_id',
        'course_id',
        'credit_unit',
        'course_type',
    ];

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\ProgramCurriculumCourse>
     */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester,\App\Models\ProgramCurriculumCourse>
     */
    public function programCurriculumSemester(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumSemester::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course,\App\Models\ProgramCurriculumCourse> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return array{course_type: 'App\Enums\CourseType', credit_unit: 'App\Enums\CreditUnit'} */
    protected function casts(): array
    {
        return [
            'course_type' => CourseType::class,
            'credit_unit' => CreditUnit::class,
        ];
    }
}
