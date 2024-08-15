<?php

declare(strict_types=1);

use App\Models\Student;
use App\Values\RegistrationNumber;
use Tests\Factories\StudentFactory;

it('throws no exception for valid matriculation number', function (): void {
    RegistrationNumber::new('EBSU/2009/00001');
    RegistrationNumber::new('EBSU/2009/00001B');
    RegistrationNumber::new('EBSU/2009/51486');
    RegistrationNumber::new('EBSU/2009/51486B');
    RegistrationNumber::new('EBSU/2009/999999');
    RegistrationNumber::new('EBSU/2009/999999B');
})->throwsNoExceptions();

it('throws exception for invalid matriculation number', function (): void {
    RegistrationNumber::new('');
    RegistrationNumber::new('51486');
    RegistrationNumber::new('2009/51486');
    RegistrationNumber::new('EBUS/2009/51486');
    RegistrationNumber::new('EBSU/209/51486');
    RegistrationNumber::new('EBSU2009/51486');
    RegistrationNumber::new('EBSU/200951486');
    RegistrationNumber::new('EBSU/2009/51486BB');
})->throws(InvalidArgumentException::class, 'Invalid registration number');

it('can get student associated with registration number', function (): void {
    $registrationNumber = 'EBSU/2009/00001';
    $student = StudentFactory::new()->create(['registration_number' => $registrationNumber]);

    $registrationNumberValue = RegistrationNumber::new($registrationNumber);

    expect($registrationNumberValue->student())->toBeInstanceOf(Student::class)
        ->and($registrationNumberValue->student()->registration_number)->toBe($student->registration_number);
});

it('throws an exception when student associated with registration number is not found', function (): void {
    RegistrationNumber::new('EBSU/2009/00001')->student();
})->throws(Exception::class, 'STUDENT NOT FOUND: Download Student Records and Try Again');

it('can get the year in a registration number', function (): void {
    $registrationNumber = 'EBSU/2009/00001';

    $year = RegistrationNumber::new($registrationNumber)->year();

    expect($year)->toBeInt()->and($year)->toBe(2009);
});

it('can get the session associated with a registration number', function (): void {
    $registrationNumber = 'EBSU/2009/00001';

    $session = RegistrationNumber::new($registrationNumber)->session();

    expect($session)->toBeString()->and($session)->toBe('2009/2010');
});
