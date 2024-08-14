<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Repositories\DepartmentRepository;

final readonly class DepartmentRecordController
{
    public function __construct(private DepartmentRepository $repository)
    {
    }

    /** @throws \Exception */
    public function getAndSaveDepartments(): void
    {
        $departments = $this->repository->getDepartments();

        $this->repository->saveDepartments($departments);

        dd($departments);
    }

    /** @throws \Exception */
    public function getAndSaveDepartment(string $departmentId): void
    {
        $department = $this->repository->getDepartment($departmentId);

        $this->repository->saveDepartment($department);

        dd($department);
    }
}
