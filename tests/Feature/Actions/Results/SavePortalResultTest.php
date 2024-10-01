<?php

declare(strict_types=1);

use App\Actions\Results\SavePortalResult;
use App\Data\Download\PortalResultData;
use App\Enums\ImportEventType;
use App\Enums\RawDataStatus;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\RawResultFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(SavePortalResult::class);

it('can save portal result into raw results table', function (): void {
    $result = [
        'exam_score' => '40',
        'grade' => 'B',
        'id' => 1,
        'in_course' => '20',
        'registration_id' => '1',
        'registration_number' => 'EBSU/2009/51486',
        'total_score' => '60',
        'upload_date' => '21-09-2000',
    ];
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::RESULTS]);
    $data = PortalResultData::fromArray($result);

    (new SavePortalResult())->execute($event, $data);

    assertDatabaseHas('raw_results', [
        'online_id' => $result['id'],
        'status' => RawDataStatus::PENDING->value,
    ]);
});

it('does not save duplicate portal result', function (): void {
    $result = [
        'exam_score' => '40',
        'grade' => 'B',
        'id' => 1,
        'in_course' => '20',
        'registration_id' => '1',
        'registration_number' => 'EBSU/2009/51486',
        'total_score' => '60',
        'upload_date' => '21-09-2000',
    ];

    RawResultFactory::new()->createOne([
        'online_id' => $result['id'],
        'status' => RawDataStatus::PROCESSED,
    ]);

    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::RESULTS]);
    $data = PortalResultData::fromArray($result);

    (new SavePortalResult())->execute($event, $data);

    assertDatabaseCount('raw_results', 1);
});
