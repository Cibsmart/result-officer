<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\StudentClient;
use App\Data\Ingest\PortalStudentData;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class StudentService
{
    public function __construct(private StudentClient $client)
    {
    }

    public function getStudentByRegistration(string $registrationNumber): PortalStudentData
    {
        $registrationNumber = Str::replace('/', '-', $registrationNumber);

        $student = $this->client->fetchStudentByRegistrationNumber($registrationNumber);

        return PortalStudentData::from($student);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalStudentData> */
    public function getStudentsByDepartmentAndSession(string $departmentId, string $session): Collection
    {
        $session = Str::replace('/', '-', $session);

        $students = $this->client->fetchStudentsByDepartmentAndSession($departmentId, $session);

        return PortalStudentData::collect(collect($students));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalStudentData> */
    public function getStudentsBySession(string $session): Collection
    {
        $session = Str::replace('/', '-', $session);

        $student = $this->client->fetchStudentsBySession($session);

        return PortalStudentData::collect(collect($student));
    }
}
