<?php

declare(strict_types=1);

use App\Values\MatriculationNumber;

test('throws no exception for valid matriculation number', function (): void {
    MatriculationNumber::new('EBSU/2009/00001');
    MatriculationNumber::new('EBSU/2009/00001B');
    MatriculationNumber::new('EBSU/2009/51486');
    MatriculationNumber::new('EBSU/2009/51486B');
    MatriculationNumber::new('EBSU/2009/999999');
    MatriculationNumber::new('EBSU/2009/999999B');
})->throwsNoExceptions();

test('throws exception for negative matriculation number', function (): void {
    MatriculationNumber::new('');
    MatriculationNumber::new('51486');
    MatriculationNumber::new('2009/51486');
    MatriculationNumber::new('EBUS/2009/51486');
    MatriculationNumber::new('EBSU/209/51486');
    MatriculationNumber::new('EBSU2009/51486');
    MatriculationNumber::new('EBSU/200951486');
    MatriculationNumber::new('EBSU/2009/51486BB');
})->throws(InvalidArgumentException::class, 'Invalid matriculation number');
