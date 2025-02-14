<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Data\Grading\GradingSchemeData;
use App\Enums\Grade;
use App\Models\RecordsUnitHead;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class TranscriptData extends Data
{
    public function __construct(
        public readonly string $recordsUnitHead,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Grading\GradingSchemeData> */
        public readonly Collection $gradingSchemes,
    ) {
    }

    public static function fromModel(bool $isEGradeAllowed = true): self
    {
        $recordHead = RecordsUnitHead::query()->where('is_current', true)->firstOrFail()->name;

        $cases = collect([
            GradingSchemeData::from(Grade::A),
            GradingSchemeData::from(Grade::B),
            GradingSchemeData::from(Grade::C),
            GradingSchemeData::from(Grade::D),
            GradingSchemeData::from(Grade::E),
            GradingSchemeData::from(Grade::F, $isEGradeAllowed),
        ]);

        $grades = $isEGradeAllowed
            ? $cases
            : $cases->except([4]);

        return new self(
            recordsUnitHead: $recordHead,
            gradingSchemes: GradingSchemeData::collect($grades),
        );
    }
}
