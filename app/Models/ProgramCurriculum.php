<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryMode;
use App\Values\SessionValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class ProgramCurriculum extends Model
{
    public static function getOrCreateFromExcelImport(ExcelImportEvent $event, RawCurriculumCourse $rawCurriculum): self
    {
        $curriculum = Curriculum::getUsingCode($rawCurriculum->curriculum);
        $entrySession = SessionValue::new($rawCurriculum->entry_session)->getSession();
        $entryMode = EntryMode::get($rawCurriculum->entry_mode);
        $programId = $event->data['program_id'];

        $programCurriculum = self::query()
            ->where('curriculum_id', $curriculum->id)
            ->where('entry_session_id', $entrySession->id)
            ->where('entry_mode', $entryMode)
            ->where('program_id', $programId)
            ->first();

        if ($programCurriculum) {
            return $programCurriculum;
        }

        $programCurriculum = new self();

        $programCurriculum->program_id = $programId;
        $programCurriculum->curriculum_id = $curriculum->id;
        $programCurriculum->entry_session_id = $entrySession->id;
        $programCurriculum->entry_mode = $entryMode;

        $programCurriculum->save();

        return $programCurriculum;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Curriculum,static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Curriculum,$this>
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,$this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'entry_session_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumLevel, static>
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumLevel, $this>
     */
    public function programCurriculumLevels(): HasMany
    {
        return $this->HasMany(ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel, static>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel, $this>
     */
    public function programCurriculumSemesters(): HasManyThrough
    {
        return $this->hasManyThrough(ProgramCurriculumSemester::class, ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, static>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, $this>
     */
    public function programCurriculumCourses(): HasManyThrough
    {
        $result = $this->through('programCurriculumSemesters')->has('programCurriculumCourses');
        assert($result instanceof HasManyThrough);

        return $result;
    }
}
