<?php

declare(strict_types=1);

use App\Values\CourseCode;

it('creates course code value object', function (): void {
    $valueObject = new CourseCode('CSC 101');

    expect($valueObject)->toBeInstanceOf(CourseCode::class);
});

it('creates valid BMAS course codes', function (string $code): void {
    expect(CourseCode::new($code)->value)->toBe($code);
})->with([
    ['CSC 101', 'normal code'],
    ['CSC 101A', 'code with alphabet suffix'],
]);

it('creates valid CCMAS course codes', function (string $code): void {
    expect(CourseCode::new($code)->value)->toBe($code);
})->with([
    ['EBSU-CSC 101', 'normal code'],
    ['EBSU-CSC 101A', 'code with alphabet suffix'],
]);

it('creates valid BMAS course codes with four characters', function (string $code): void {
    expect(CourseCode::new($code)->value)->toBe($code);
})->with([
    ['ARCH 101', 'archaeology code'],
    ['ARST 101', 'architecture code'],
]);

it('recursively validates course code with multiple codes', function (string $code): void {
    expect(CourseCode::new($code)->value)->toBe($code);
})->with([
    ['CSC 101/CSC 111'],
    ['CSC 101/CSC 111/CSC 112'],
]);

it('trims leading and trailing spaces in course code', function (): void {
    expect(CourseCode::new(' CSC 101 ')->value)->toBe('CSC 101');
});

it('throws invalid argument exception for invalid BMAS course codes', function (string $code): void {
    CourseCode::new($code);
})->throws(InvalidArgumentException::class, 'Invalid course code')
    ->with([
        ['CSC101', 'no space'],
        ['CSC 1010', 'four digits'],
        ['CSC-101', 'hyphen instead of space'],
        ['CSC/101', 'slash instead of space'],
        ['CSC  101', 'double spacing'],
        ['CSC 101 A', 'space btw digits and character'],
    ]);

it('throws invalid argument exception for invalid CCMAS course codes', function (string $code): void {
    CourseCode::new($code);
})->throws(InvalidArgumentException::class, 'Invalid course code')
    ->with([
        ['EBSU/CSC 101', 'slash instead of hyphen'],
        ['EBUS-CSC 101', 'EBUS instead of EBSU'],
    ]);