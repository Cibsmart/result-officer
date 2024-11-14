<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProgramCurriculumLevel extends Model
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel>
     */
    public function programCurriculumSemesters(): HasMany
    {
        return $this->HasMany(ProgramCurriculumSemester::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\ProgramCurriculumLevel> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
