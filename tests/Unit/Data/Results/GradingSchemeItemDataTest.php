<?php

declare(strict_types=1);

use App\Data\Grading\GradingSchemeData;
use App\Enums\Grade;

test('grading scheme item data is correct for all grades', function (Grade $grade): void {
    $gradingSchemeItemData = GradingSchemeData::from($grade);

    $min = $grade->min();
    $max = $grade->max();

    expect($gradingSchemeItemData)->toBeInstanceOf(GradingSchemeData::class)
        ->and($gradingSchemeItemData->grade)->toBe($grade->name)
        ->and($gradingSchemeItemData->gradePoint)->toBe($grade->point())
        ->and($gradingSchemeItemData->range)->toBe("$min - $max")
        ->and($gradingSchemeItemData->interpretation)->toBe($grade->interpretation());
})->with([Grade::A, Grade::B, Grade::C, Grade::D, Grade::E, Grade::F]);

test('grading scheme item data is correct for E and F grades without E Grading', function (Grade $grade): void {
    $gradingSchemeItemData = GradingSchemeData::from($grade, false);

    $min = $grade->min();
    $max = Grade::E->max();

    expect($gradingSchemeItemData)->toBeInstanceOf(GradingSchemeData::class)
        ->and($gradingSchemeItemData->grade)->toBe($grade->name)
        ->and($gradingSchemeItemData->gradePoint)->toBe($grade->point())
        ->and($gradingSchemeItemData->range)->toBe("$min - $max")
        ->and($gradingSchemeItemData->interpretation)->toBe($grade->interpretation());
})->with([Grade::F]);
