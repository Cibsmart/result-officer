<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Http\Controllers\Controller;
use App\Repositories\DepartmentRepository;

final class DepartmentRecordController extends Controller
{
    public function __construct(public DepartmentRepository $repository)
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
