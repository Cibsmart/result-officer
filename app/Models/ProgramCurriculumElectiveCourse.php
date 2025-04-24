<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProgramCurriculumElectiveCourse extends Model
{
    public static function getOrCreateUsingElectiveGroupAndCourse(
        ProgramCurriculumCourse $curriculumCourse,
        ProgramCurriculumElectiveGroup $electiveGroup,
    ): void {
        self::firstOrCreate([
            'program_curriculum_course_id' => $curriculumCourse->id,
            'program_curriculum_elective_group_id' => $electiveGroup->id,
        ]);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumElectiveGroup, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumElectiveGroup, static>
     */
    public function programCurriculumElectiveGroup(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumElectiveGroup::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumCourse, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumCourse, static>
     */
    public function programCurriculumCourse(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumCourse::class);
    }
}
