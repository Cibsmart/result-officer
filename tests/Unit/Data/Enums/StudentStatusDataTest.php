<?php

declare(strict_types=1);

use App\Data\Enums\StudentStatusData;
use App\Data\Enums\StudentStatusListData;
use App\Enums\StudentStatus;

it('return a valid dto from enum', function (): void {
    $status = StudentStatus::ACTIVE;

    $data = StudentStatusData::fromEnum($status);

    expect($data)->toBeInstanceOf(StudentStatusData::class)
        ->and($data->id)->toBe($status->value)
        ->and($data->name)->toBe($status->name);
});

it('returns list of status dto', function (): void {
    $data = StudentStatusListData::new();

    expect($data)->toBeInstanceOf(StudentStatusListData::class)
        ->and($data->data->count())->toBe(count(StudentStatus::cases()) + 1);
});
