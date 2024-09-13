<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\Download\PortalDepartmentData;
use App\Data\Response\ResponseData;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Services\Api\DepartmentService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class DepartmentRepository
{
    public function __construct(public DepartmentService $service)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalDepartmentData>
     * @throws \Exception
     */
    public function getDepartments(): Collection
    {
        return $this->service->getAllDepartments();
    }

    /** @throws \Exception */
    public function getDepartment(int $departmentId): PortalDepartmentData
    {
        $department = $this->service->getDepartmentDetail($departmentId)->first();

        assert($department instanceof PortalDepartmentData);

        return $department;
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalDepartmentData> $departments
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveDepartments(Collection $departments): Collection
    {

        $results = [];

        foreach ($departments as $department) {
            try {
                $this->saveDepartment($department);
                $results[] = ResponseData::from([$department->departmentCode, true]);
            } catch (Exception $e) {
                $results[] = ResponseData::from([$department->departmentCode, $e->getMessage()]);

                continue;
            }
        }

        return collect($results);
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

    /** @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalProgramData> $programs */
    private function createProgramsForDepartment(Department $dbDepartment, Collection $programs): void
    {
        if ($programs->count() === 0) {
            $this->createProgram($dbDepartment->name, $dbDepartment->id, $dbDepartment->code);

            return;
        }

        foreach ($programs as $program) {
            $programCode = Str::of($program->name)->limit(3, '')->value();

            $this->createProgram($program->name, $dbDepartment->id, $programCode);
        }
    }

    private function createProgram(
        string $programName,
        int $departmentId,
        string $programCode,
    ): void {
        Program::firstOrCreate(
            ['name' => $programName],
            [
                'code' => $programCode,
                'department_id' => $departmentId,
                'program_type_id' => 5,
            ],
        );
    }
}
