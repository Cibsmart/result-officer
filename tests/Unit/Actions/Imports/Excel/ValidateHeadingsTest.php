<?php

declare(strict_types=1);

use App\Actions\Imports\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;

test('a perfect heading match is not overwritten by a weaker later collision', function (): void {
    // RES4.xlsx shipped both "registration_number" and "old_registration_number".
    // similar_text() scores the latter at ~90% against the registration_number
    // aliases, which previously clobbered the exact 100% match (last write wins),
    // silently mapping every row to the always-null old_registration_number column.
    $headings = [
        'sn', 'students_name', 'registration_number', 'old_registration_number',
        'in_course_1', 'in_course_2', 'exam', 'total', 'grade', 'credit_unit',
        'semester', 'session', 'course_code', 'course_title', 'students_department',
        'examiners_name', 'examiners_department', 'exam_date',
    ];

    $result = (new ValidateHeadings())->execute($headings, ExcelImportType::RESULT);

    expect($result['passed'])->toBeTrue()
        ->and($result['validated']['registration_number'])->toBe('registration_number');
});

test('heading order does not change which column wins a collision', function (): void {
    $mappedColumn = function (array $registrationColumns): string {
        $headings = [...$registrationColumns, ...baseResultHeadings()];

        return (new ValidateHeadings())->execute(
            $headings,
            ExcelImportType::RESULT,
        )['validated']['registration_number'];
    };

    expect($mappedColumn(['registration_number', 'old_registration_number']))->toBe('registration_number')
        ->and($mappedColumn(['old_registration_number', 'registration_number']))->toBe('registration_number');
});

/** @return array<int, string> The remaining required RESULT headings, minus registration_number. */
function baseResultHeadings(): array
{
    return [
        'sn', 'students_name', 'in_course_1', 'in_course_2', 'exam', 'total', 'grade',
        'credit_unit', 'semester', 'session', 'course_code', 'course_title',
        'students_department', 'examiners_name', 'examiners_department', 'exam_date',
    ];
}
