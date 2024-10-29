<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\ProgramCurriculum> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester,\App\Models\ProgramCurriculum> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,\App\Models\ProgramCurriculum> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumCourse> */
    public function courses(): HasMany
    {
        return $this->HasMany(ProgramCurriculumCourse::class);
    }
}
