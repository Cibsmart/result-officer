<?php

declare(strict_types=1);

use App\Actions\Registrations\SavePortalRegistration;
use App\Data\Download\PortalRegistrationData;
use App\Enums\ImportEventType;
use App\Enums\RawDataStatus;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\RawRegistrationFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(SavePortalRegistration::class);

it('can save portal registration into raw registrations table', function (): void {
    $registration = [
        'course_id' => '1',
        'course_title' => 'Introduction to Computer Science',
        'credit_unit' => '3',
        'id' => '1',
        'level' => '100',
        'registration_date' => '28-10-2009',
        'registration_number' => 'EBSU/2009/51486',
        'semester' => 'FIRST',
        'session' => '2009-2010',
    ];
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::REGISTRATIONS]);
    $data = PortalRegistrationData::fromArray($registration);

    (new SavePortalRegistration())->execute($event, $data);

    assertDatabaseHas('raw_registrations', [
        'level' => $registration['level'],
        'online_id' => $registration['id'],
        'registration_number' => $registration['registration_number'],
        'session' => $registration['session'],
        'status' => RawDataStatus::PENDING->value,
    ]);
});

it('does not save duplicate portal registration', function (): void {
    $registration = [
        'course_id' => '1',
        'course_title' => 'Introduction to Computer Science',
        'credit_unit' => '3',
        'id' => '1',
        'level' => '100',
        'registration_date' => '28-10-2009',
        'registration_number' => 'EBSU/2009/51486',
        'semester' => 'FIRST',
        'session' => '2009-2010',
    ];

    RawRegistrationFactory::new()->createOne([
        'online_id' => $registration['id'],
        'status' => RawDataStatus::PROCESSED,
    ]);

    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::REGISTRATIONS]);
    $data = PortalRegistrationData::fromArray($registration);

    (new SavePortalRegistration())->execute($event, $data);

    assertDatabaseCount('raw_registrations', 1);
});
