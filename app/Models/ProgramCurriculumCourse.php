<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProgramCurriculumCourse extends Model
{
    use SoftDeletes;

    public static function createFromExcelImport(
        ProgramCurriculumSemester $curriculumSemester,
        RawCurriculumCourse $curriculumCourse,
        Course $course,
    ): self {

        $creditUnit = CreditUnit::from($curriculumCourse->credit_unit);
        $courseType = CourseType::fromNameOrCode($curriculumCourse->course_type);
        assert($courseType instanceof CourseType);

        $programCurriculumCourse = new self();

        $programCurriculumCourse->program_curriculum_semester_id = $curriculumSemester->id;
        $programCurriculumCourse->course_id = $course->id;
        $programCurriculumCourse->course_type = $courseType;
        $programCurriculumCourse->credit_unit = $creditUnit;

        $programCurriculumCourse->save();

        return $programCurriculumCourse;
    }

    public static function getUsingId(int $programCourseId): self
    {
        return self::query()->where('id', $programCourseId)->firstOrFail();
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, $this>
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, static>
     */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester,static>
     */
    public function programCurriculumSemester(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumSemester::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CourseAlternative, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CourseAlternative, static>
     */
    public function courseAlternatives(): HasMany
    {
        return $this->hasMany(CourseAlternative::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course,$this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course,static>
     */
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
