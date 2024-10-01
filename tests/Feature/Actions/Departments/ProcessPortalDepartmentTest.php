<?php

declare(strict_types=1);

use App\Actions\Departments\ProcessPortalDepartment;
use App\Enums\RawDataStatus;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\ProgramTypeFactory;
use Tests\Factories\RawDepartmentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProcessPortalDepartment::class);

it('can process raw department and save into the departments table', function (): void {
    $rawDepartment = RawDepartmentFactory::new()->createOne();
    ProgramTypeFactory::new()->createOne(['id' => '5']);

    (new ProcessPortalDepartment())->execute($rawDepartment);

    expect($rawDepartment->fresh()->status)->toBe(RawDataStatus::PROCESSED);

    assertDatabaseHas('faculties', [
        'name' => strtoupper($rawDepartment->faculty),
    ]);

    assertDatabaseHas('departments', [
        'code' => strtoupper($rawDepartment->code),
        'name' => strtoupper($rawDepartment->name),
        'online_id' => $rawDepartment->online_id,
    ]);

    assertDatabaseHas('programs', [
        'name' => strtoupper($rawDepartment->name),
    ]);
});

it('does not save save duplicate department into the departments table', function (): void {
    $department = DepartmentFactory::new()->createOne();
    $rawDepartment = RawDepartmentFactory::new()->createOne(['code' => $department->code, 'name' => $department->name]);

    (new ProcessPortalDepartment())->execute($rawDepartment);

    expect($rawDepartment->fresh()->status)->toBe(RawDataStatus::DUPLICATE);
});
