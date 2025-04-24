<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProgramCurriculumLevel extends Model
{
    public static function getOrCreateFromExcelImport(ProgramCurriculum $programCurriculum, Level $level): self
    {
        $curriculumLevel = self::query()
            ->where('program_curriculum_id', $programCurriculum->id)
            ->where('level_id', $level->id)
            ->first();

        if ($curriculumLevel) {
            return $curriculumLevel;
        }

        $curriculumLevel = new self();

        $curriculumLevel->program_curriculum_id = $programCurriculum->id;
        $curriculumLevel->level_id = $level->id;

        $curriculumLevel->save();

        return $curriculumLevel;
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculum, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculum, static>
     */
    public function programCurriculum(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculum::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumSemester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumSemester, static>
     */
    public function programCurriculumSemesters(): HasMany
    {
        return $this->HasMany(ProgramCurriculumSemester::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, static>
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
