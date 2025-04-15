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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, static>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Curriculum,$this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Curriculum,static>
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,$this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session,static>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'entry_session_id');
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumLevel, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumLevel, static>
     */
    public function programCurriculumLevels(): HasMany
    {
        return $this->HasMany(ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel, $this>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\ProgramCurriculumSemester, \App\Models\ProgramCurriculumLevel, static>
     */
    public function programCurriculumSemesters(): HasManyThrough
    {
        return $this->hasManyThrough(ProgramCurriculumSemester::class, ProgramCurriculumLevel::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, $this>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, static>
     */
    public function programCurriculumCourses(): HasManyThrough
    {
        $result = $this->through('programCurriculumSemesters')->has('programCurriculumCourses');
        assert($result instanceof HasManyThrough);

        return $result;
    }
}
