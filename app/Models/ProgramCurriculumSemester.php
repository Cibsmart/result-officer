<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreditUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class ProgramCurriculumSemester extends Model
{
    /** @param array<string, int> $data */
    public static function getOrCreateFromExcelImport(
        ProgramCurriculumLevel $curriculumLevel,
        Semester $semester,
        array $data,
    ): self {
        $curriculumSemester = self::query()
            ->where('program_curriculum_level_id', $curriculumLevel->id)
            ->where('semester_id', $semester->id)
            ->first();

        if ($curriculumSemester) {
            return $curriculumSemester;
        }

        $curriculumSemester = new self();

        $curriculumSemester->program_curriculum_level_id = $curriculumLevel->id;
        $curriculumSemester->semester_id = $semester->id;
        $curriculumSemester->minimum_elective_count = $data['minimum_elective_count'];
        $curriculumSemester->minimum_elective_units = CreditUnit::from($data['minimum_elective_unit']);

        $curriculumSemester->save();

        return $curriculumSemester;
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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumCourse, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumCourse, static>
     */
    public function programCurriculumCourses(): HasMany
    {
        return $this->HasMany(ProgramCurriculumCourse::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumLevel, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumLevel, static>
     */
    public function programCurriculumLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumLevel::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, static>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumElectiveGroup, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumElectiveGroup, static>
     */
    public function programCurriculumElectiveGroups(): HasMany
    {
        return $this->hasMany(ProgramCurriculumElectiveGroup::class);
    }

    /** @return array{minimum_elective_units: 'App\Enums\CreditUnit'} */
    protected function casts(): array
    {
        return [
            'minimum_elective_units' => CreditUnit::class,
        ];
    }
}
