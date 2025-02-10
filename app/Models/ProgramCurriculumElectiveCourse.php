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
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumElectiveGroup, \App\Models\ProgramCurriculumElectiveCourse>
     */
    public function programCurriculumElectiveGroup(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumElectiveGroup::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumCourse, \App\Models\ProgramCurriculumElectiveCourse>
     */
    public function programCurriculumCourse(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumCourse::class);
    }
}
