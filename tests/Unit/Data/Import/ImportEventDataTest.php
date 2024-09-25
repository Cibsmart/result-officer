<?php

declare(strict_types=1);

use App\Data\Import\ImportEventData;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use Tests\Factories\ImportEventFactory;

test('import event data is correct', function (): void {
    $event = ImportEventFactory::new()->createOne(['data' => ['courses' => 'all', 'session' => '2009-2010']]);

    $data = ImportEventData::fromModel($event);

    expect($data)->toBeInstanceOf(ImportEventData::class)
        ->and($data->target)->toBeString()->toBe($event->user->name)
        ->and($data->type)->toBeInstanceOf(ImportEventType::class)
        ->and($data->status)->toBeInstanceOf(ImportEventStatus::class)
        ->and($data->description)->toBeString()->toBe('downloaded: 0')
        ->and($data->content)->toBeString()->toBe('downloaded ALL COURSES, 2009-2010 SESSION.')
        ->and($data->date)->toBeString()->toBe($event->created_at->diffForHumans());
});
