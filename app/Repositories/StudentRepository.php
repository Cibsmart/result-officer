<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingStudent;
use App\Data\Download\PortalStudentData;
use App\Data\Response\ResponseData;
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
        $student = $this->service->getStudentByRegistrationNumber($registrationNumber)->first();

        assert($student instanceof PortalStudentData);

        return $student;
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsByDepartmentAndSession(int $departmentId, string $session): Collection
    {
        return $this->service->getStudentsByDepartmentAndSession($departmentId, $session);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsBySession(string $session): Collection
    {
        return $this->service->getStudentsBySession($session);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> $students
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveStudents(Collection $students): Collection
    {
        $results = [];

        foreach ($students as $student) {
            try {
                $this->saveStudent($student);
                $results[] = ResponseData::from([$student->registrationNumber, true]);
            } catch (ValueError $e) {
                $results[] = ResponseData::from([$student->registrationNumber, $e->getMessage()]);

                continue;
            } catch (Exception $e) {
                $results[] = ResponseData::from([$student->registrationNumber, $e->getMessage()]);

                continue;
            }
        }

        return ResponseData::collect(collect($results));
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
