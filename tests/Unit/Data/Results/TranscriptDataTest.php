<?php

declare(strict_types=1);

use App\Data\Results\TranscriptData;
use App\Enums\Grade;
use Tests\Factories\RecordsUnitHeadFactory;

test('transcript data is correct for students with E grade', function (): void {
    $recordHead = RecordsUnitHeadFactory::new()->active()->createOne();

    $transcriptData = TranscriptData::from();

    expect($transcriptData)->toBeInstanceOf(TranscriptData::class)
        ->and($transcriptData->recordsUnitHead)->toBe($recordHead->name)
        ->and($transcriptData->gradingSchemes->count())->toBe(6);
});

test('transcript data is correct students without E grade', function (): void {
    $recordHead = RecordsUnitHeadFactory::new()->active()->createOne();

    $transcriptData = TranscriptData::from(false);
    $lastData = $transcriptData->gradingSchemes->last();

    expect($transcriptData)->toBeInstanceOf(TranscriptData::class)
        ->and($transcriptData->recordsUnitHead)->toBe($recordHead->name)
        ->and($transcriptData->gradingSchemes->count())->toBe(5)
        ->and($lastData->grade)->toBe(Grade::F->name)
        ->and($lastData->range)->toBe('0 - 44');
});
