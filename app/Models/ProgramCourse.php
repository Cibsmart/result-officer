<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProgramCourse extends Model
{

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculum,\App\Models\ProgramCourse> */
    public function programCurriculum(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculum::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course,\App\Models\ProgramCourse> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CreditUnit, \App\Models\ProgramCourse> */
    public function creditUnit(): BelongsTo
    {
        return $this->belongsTo(CreditUnit::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CourseType, \App\Models\ProgramCourse> */
    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

}
