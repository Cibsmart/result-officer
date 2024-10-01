<?php

declare(strict_types=1);

use App\Actions\Students\SavePortalStudent;
use App\Data\Download\PortalStudentData;
use App\Enums\ImportEventType;
use App\Enums\RawDataStatus;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\RawStudentFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(SavePortalStudent::class);

it('can save portal student into raw students table', function (): void {
    $student = [
        'date_of_birth' => '27-07-1985',
        'department_id' => '1',
        'email' => 'test@gmail.com',
        'entry_level' => '100',
        'entry_mode' => 'UTME',
        'entry_session' => '2009-2010',
        'first_name' => 'Firstname',
        'gender' => 'M',
        'id' => '1',
        'jamb_registration_number' => '12345678JH',
        'last_name' => 'Lastname',
        'local_government' => 'Abakaliki',
        'option' => '',
        'other_names' => 'Othernames',
        'phone_number' => '803-245-2456',
        'registration_number' => 'EBSU/2009/51486',
        'state' => 'Ebonyi',
    ];
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::STUDENTS]);
    $data = PortalStudentData::fromArray($student);

    (new SavePortalStudent())->execute($event, $data);

    assertDatabaseHas('raw_students', [
        'date_of_birth' => $student['date_of_birth'],
        'department_id' => $student['department_id'],
        'entry_level' => $student['entry_level'],
        'entry_mode' => $student['entry_mode'],
        'entry_session' => $student['entry_session'],
        'first_name' => $student['first_name'],
        'gender' => $student['gender'],
        'last_name' => $student['last_name'],
        'online_id' => $student['id'],
        'option' => $student['option'],
        'other_names' => $student['other_names'],
        'registration_number' => $student['registration_number'],
        'status' => RawDataStatus::PENDING->value,
    ]);
});

it('does not save duplicate portal student', function (): void {
    $student = [
        'date_of_birth' => '27-07-1985',
        'department_id' => '1',
        'email' => 'test@gmail.com',
        'entry_level' => '100',
        'entry_mode' => 'UTME',
        'entry_session' => '2009-2010',
        'first_name' => 'Firstname',
        'gender' => 'M',
        'id' => '1',
        'jamb_registration_number' => '12345678JH',
        'last_name' => 'Lastname',
        'local_government' => 'Abakaliki',
        'option' => '',
        'other_names' => 'Othernames',
        'phone_number' => '803-245-2456',
        'registration_number' => 'EBSU/2009/51486',
        'state' => 'Ebonyi',
    ];

    RawStudentFactory::new()->createOne([
        'registration_number' => $student['registration_number'],
        'status' => RawDataStatus::PROCESSED,
    ]);
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::STUDENTS]);
    $data = PortalStudentData::fromArray($student);

    (new SavePortalStudent())->execute($event, $data);

    assertDatabaseCount('raw_students', 1);
});
