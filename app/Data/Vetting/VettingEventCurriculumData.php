<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Data\Curriculum\ProgramCurriculumData;
use App\Models\ProgramCurriculum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingEventCurriculumData extends Data
{
    /** @param \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingData> $vettings */
    public function __construct(
        public readonly ProgramCurriculumData $curriculum,
        public readonly Collection $vettings,
    ) {
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingData> $vettingList */
    public static function fromGroupData(int|string $programCurriculumId, Collection $vettingList): self
    {
        if ($programCurriculumId === '') {
            return new self(ProgramCurriculumData::getEmpty(), $vettingList);
        }

        $curriculum = ProgramCurriculum::query()
            ->with('program', 'curriculum', 'session')
            ->where('id', $programCurriculumId)
            ->firstOrFail();

        $programCurriculum = ProgramCurriculumData::fromModel($curriculum);

        return new self($programCurriculum, $vettingList);
    }
}
