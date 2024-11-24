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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumCourse, \App\Models\ProgramCurriculumSemester>
     */
    public function programCurriculumCourses(): HasMany
    {
        return $this->HasMany(ProgramCurriculumCourse::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumLevel, \App\Models\ProgramCurriculumSemester>
     */
    public function programCurriculumLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, \App\Models\ProgramCurriculumSemester>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumElectiveGroup, \App\Models\ProgramCurriculumSemester>
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
