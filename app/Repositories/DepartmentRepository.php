<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\Ingest\PortalDepartmentData;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Services\Api\DepartmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class DepartmentRepository
{
    public function __construct(public DepartmentService $service)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalDepartmentData>
     * @throws \Exception
     */
    public function getDepartments(): Collection
    {
        return $this->service->getAllDepartments();
    }

    /** @throws \Exception */
    public function getDepartment(string $departmentId): PortalDepartmentData
    {
        return $this->service->getDepartmentDetail($departmentId);
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalDepartmentData> $departments */
    public function saveDepartments(Collection $departments): void
    {
        foreach ($departments as $department) {
            $this->saveDepartment($department);
        }
    }

    public function saveDepartment(PortalDepartmentData $department): Department
    {

        $faculty = $this->getOrCreateFaculty($department->facultyName);

        $dbDepartment = $this->getOrCreateDepartment($department, $faculty);

        $this->createProgramsForDepartment($dbDepartment, $department->programs);

        return $dbDepartment;
    }

    private function getOrCreateFaculty(string $facultyName): Faculty
    {
        $facultyCode = Str::of($facultyName)->prepend('F ')
            ->explode(' ')
            ->map(fn ($word) => $word[0])
            ->join('');

        return Faculty::firstOrCreate(
            ['name' => $facultyName],
            ['code' => $facultyCode],
        );
    }

    private function getOrCreateDepartment(PortalDepartmentData $department, Faculty $faculty): Department
    {
        return Department::firstOrCreate(
            ['name' => $department->departmentName],
            [
                'code' => $department->departmentCode,
                'faculty_id' => $faculty->id,
                'online_id' => $department->onlineId,
            ],
        );
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalProgramData> $programs */
    private function createProgramsForDepartment(Department $dbDepartment, Collection $programs): void
    {
        if ($programs->count() === 0) {
            $this->createProgram($dbDepartment->name, $dbDepartment->id, $dbDepartment->code);

            return;
        }

        foreach ($programs as $program) {
            $programCode = Str::of($program->name)->limit(3, '')->value();

            $this->createProgram($program->name, $dbDepartment->id, $programCode,
                ['online_id' => $program->id]);
        }
    }

    /** @param array<string, int|string> $attributes */
    private function createProgram(
        string $programName,
        int $departmentId,
        string $programCode,
        array $attributes = [],
    ): void {
        Program::firstOrCreate(
            ['name' => $programName],
            [
                ...$attributes,
                'code' => $programCode,
                'department_id' => $departmentId,
                'program_type_id' => 5,
            ],
        );
    }
}
