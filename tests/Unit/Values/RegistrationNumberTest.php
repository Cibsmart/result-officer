<?php

declare(strict_types=1);

use App\Values\RegistrationNumber;

test('throws no exception for valid matriculation number', function (): void {
    RegistrationNumber::new('EBSU/2009/00001');
    RegistrationNumber::new('EBSU/2009/00001B');
    RegistrationNumber::new('EBSU/2009/51486');
    RegistrationNumber::new('EBSU/2009/51486B');
    RegistrationNumber::new('EBSU/2009/999999');
    RegistrationNumber::new('EBSU/2009/999999B');
})->throwsNoExceptions();

test('throws exception for negative matriculation number', function (): void {
    RegistrationNumber::new('');
    RegistrationNumber::new('51486');
    RegistrationNumber::new('2009/51486');
    RegistrationNumber::new('EBUS/2009/51486');
    RegistrationNumber::new('EBSU/209/51486');
    RegistrationNumber::new('EBSU2009/51486');
    RegistrationNumber::new('EBSU/200951486');
    RegistrationNumber::new('EBSU/2009/51486BB');
})->throws(InvalidArgumentException::class, 'Invalid registration number');
