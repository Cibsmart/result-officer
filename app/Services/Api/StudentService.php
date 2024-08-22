<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\StudentClient;
use App\Data\Download\PortalStudentData;
use Illuminate\Support\Collection;

final readonly class StudentService
{
    public function __construct(private StudentClient $client)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentByRegistrationNumber(string $registrationNumber): Collection
    {
        $student = $this->client->fetchStudentByRegistrationNumber($registrationNumber);

        return PortalStudentData::collect(collect($student));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsByDepartmentAndSession(string $departmentId, string $session): Collection
    {
        $students = $this->client->fetchStudentsByDepartmentAndSession($departmentId, $session);

        return PortalStudentData::collect(collect($students));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsBySession(string $session): Collection
    {
        $student = $this->client->fetchStudentsBySession($session);

        return PortalStudentData::collect(collect($student));
    }
}
