<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProgramCurriculumElectiveGroup extends Model
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumElectiveCourse, \App\Models\ProgramCurriculumElectiveGroup>
     */
    public function programCurriculumElectiveCourses(): HasMany
    {
        return $this->hasMany(ProgramCurriculumElectiveCourse::class);
    }
}
