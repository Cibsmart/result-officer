<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProgramCurriculumElectiveCourse extends Model
{
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
