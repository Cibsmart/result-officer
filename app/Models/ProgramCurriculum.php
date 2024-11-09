<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class ProgramCurriculum extends Model
{
    protected $fillable = [
        'program_id',
        'curriculum_id',
        'level_id',
        'semester_id',
        'session_id',
        'minimum_elective_units',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, \App\Models\ProgramCurriculum> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Curriculum,\App\Models\ProgramCurriculum> */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,\App\Models\ProgramCurriculum> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumLevel, \App\Models\ProgramCurriculum>
     */
    public function programCurriculumLevels(): HasMany
    {
        return $this->HasMany(ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel, \App\Models\ProgramCurriculum>
     */
    public function programCurriculumSemesters(): HasManyThrough
    {
        return $this->hasManyThrough(ProgramCurriculumSemester::class, ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumCourse, \Illuminate\Database\Eloquent\Model, \App\Models\ProgramCurriculum>
     */
    public function programCurriculumCourses(): HasManyThrough
    {
        return $this->through('programCurriculumSemesters')->has('programCurriculumCourses');
    }
}
