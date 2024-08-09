<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingStudent;
use App\Data\Ingest\PortalStudentData;
use App\Models\Student;
use App\Services\Api\StudentService;
use Exception;
use Illuminate\Support\Collection;
use ValueError;

final class StudentRepository
{
    public function __construct(public StudentService $service)
    {
    }

    public function getStudentByRegistrationNumber(string $registrationNumber): PortalStudentData
    {
        return $this->service->getStudentByRegistration($registrationNumber);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalStudentData> */
    public function getStudentsByDepartmentAndSession(string $departmentId, string $session): Collection
    {
        return $this->service->getStudentsByDepartmentAndSession($departmentId, $session);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalStudentData> */
    public function getStudentsBySession(string $session): Collection
    {
        return $this->service->getStudentsBySession($session);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalStudentData> $students
     * @return array<string, string|true>
     */
    public function saveStudents(Collection $students): array
    {
        $results = [];

        foreach ($students as $student) {
            try {
                $this->saveStudent($student);
                $results[$student->registrationNumber] = true;
            } catch (ValueError $e) {
                $results[$student->registrationNumber] = $e->getMessage();

                continue;
            } catch (Exception $e) {
                $results[$student->registrationNumber] = $e->getMessage();

                continue;
            }
        }

        return $results;
    }

    /**
     * @throws \Exception
     * @throws \ValueError
     */
    public function saveStudent(PortalStudentData $studentData): Student
    {
        $pendingStudent = PendingStudent::new($studentData);

        $pendingStudent->save();

        return $pendingStudent->student;
    }
}
