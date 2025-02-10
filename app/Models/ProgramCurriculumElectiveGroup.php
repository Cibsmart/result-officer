<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class ProgramCurriculumElectiveGroup extends Model
{
    public static function getOrCreateUsingName(ProgramCurriculumCourse $curriculumCourse, string $groupName): self
    {
        return self::query()->firstOrCreate(
            [
                'name' => $groupName,
                'program_curriculum_semester_id' => $curriculumCourse->program_curriculum_semester_id,
            ],
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\Registration> */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumElectiveCourse, \App\Models\ProgramCurriculumElectiveGroup>
     */
    public function programCurriculumElectiveCourses(): HasMany
    {
        return $this->hasMany(ProgramCurriculumElectiveCourse::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumElectiveGroup>
     */
    public function programCurriculumSemester(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumSemester::class);
    }
}
