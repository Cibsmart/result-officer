<?php

declare(strict_types=1);

use App\Actions\Import\Departments\SavePortalDepartment;
use App\Data\Download\PortalDepartmentData;
use App\Enums\ImportEventType;
use App\Enums\RawDataStatus;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\RawDepartmentFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(SavePortalDepartment::class);

it('can save portal department into raw departments table', function (): void {
    $department = ['id' => '1', 'code' => 'CSC', 'name' => 'Computer Science', 'faculty' => 'Science', 'options' => []];
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::DEPARTMENTS]);
    $data = PortalDepartmentData::fromArray($department);

    (new SavePortalDepartment())->execute($event, $data);

    assertDatabaseHas('raw_departments', [
        'code' => $department['code'],
        'faculty' => $department['faculty'],
        'import_event_id' => $event->id,
        'name' => $department['name'],
        'online_id' => $department['id'],
        'options' => json_encode($department['options']),
        'status' => RawDataStatus::PENDING->value,
    ]);
});

it('does not save duplicate portal department', function (): void {
    $department = [
        'code' => 'CSC 101',
        'faculty' => 'Science',
        'id' => '1',
        'name' => 'Computer Science',
        'options' => [],
    ];
    RawDepartmentFactory::new()->createOne([
        'code' => $department['code'],
        'faculty' => $department['faculty'],
        'name' => $department['name'],
        'status' => RawDataStatus::PROCESSED,
    ]);
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::DEPARTMENTS]);
    $data = PortalDepartmentData::fromArray($department);

    (new SavePortalDepartment())->execute($event, $data);

    assertDatabaseCount('raw_departments', 1);
});
