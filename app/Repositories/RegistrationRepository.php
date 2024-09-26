<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingRegistration;
use App\Data\Download\PortalRegistrationData;
use App\Data\Response\ResponseData;
use App\Enums\YearEnum;
use App\Models\Enrollment;
use App\Models\Level;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use App\Services\Api\RegistrationService;
use App\Values\RegistrationNumber;
use Exception;
use Illuminate\Support\Collection;

final class RegistrationRepository
{
    public function __construct(public RegistrationService $service)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        return $this->service->getRegistrationsByRegistrationNumber($registrationNumber);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentAndSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        return $this->service->getRegistrationsByDepartmentSessionAndLevel($departmentId, $session, $level);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        return $this->service->getRegistrationsByDepartmentSessionAndSemester($departmentId, $session, $semester);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsBySessionAndCourse(string $session, int $courseId): Collection
    {
        return $this->service->getRegistrationsBySessionAndCourse($session, $courseId);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveRegistrations(Collection $registrations): Collection
    {
        $results = [];

        $studentRegistrations = $registrations->groupBy('registrationNumber');

        foreach ($studentRegistrations as $registrationNumber => $registrations) {
            try {
                $this->saveStudentRegistrations($registrationNumber, $registrations);
                $results[] = ResponseData::from([$registrationNumber, true]);
            } catch (Exception $e) {
                $results[] = ResponseData::from([$registrationNumber, $e->getMessage()]);

                continue;
            }
        }

        return ResponseData::collect(collect($results));
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @throws \Exception
     */
    private function saveStudentRegistrations(string $registrationNumber, Collection $registrations): void
    {
        $student = RegistrationNumber::new($registrationNumber)->student();

        $sessionRegistrations = $registrations->groupBy('session');

        foreach ($sessionRegistrations as $session => $registrations) {
            $session = $this->getSession($session);
            $level = $this->getLevel($registrations->firstOrFail()->level);

            $this->saveStudentSessionRegistrations($student, $session, $level, $registrations);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @throws \Exception
     */
    private function saveStudentSemesterRegistrations(
        Enrollment $sessionEnrollment,
        string $semester,
        Collection $registrations,
    ): void {
        $semester = $this->getSemester($semester);

        $semesterEnrollment = (new SemesterEnrollment())->firstOrCreate(
            ['enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
        );

        foreach ($registrations as $registration) {
            $this->saveRegistration($semesterEnrollment, $registration);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @throws \Exception
     */
    private function saveStudentSessionRegistrations(
        Student $student,
        Session $session,
        Level $level,
        Collection $registrations,
    ): void {
        $sessionEnrollment = (new Enrollment())->firstOrCreate(
            ['student_id' => $student->id, 'session_id' => $session->id, 'level_id' => $level->id],
            ['year_id' => YearEnum::FIRST->value],
        );

        $semesterRegistrations = $registrations->groupBy('semester');

        foreach ($semesterRegistrations as $semester => $registrations) {

            $this->saveStudentSemesterRegistrations($sessionEnrollment, $semester, $registrations);
        }
    }

    /** @throws \Exception */
    private function saveRegistration(
        SemesterEnrollment $semesterEnrollment,
        PortalRegistrationData $registrationData,
    ): void {

        $pendingRegistration = PendingRegistration::new($semesterEnrollment, $registrationData);

        $pendingRegistration->save();
    }

    private function getSession(string $session): Session
    {
        return Session::query()->where('name', $session)->firstOrFail();
    }

    private function getLevel(string $level): Level
    {
        return Level::query()->where('name', $level)->firstOrFail();
    }

    private function getSemester(string $semester): Semester
    {
        return Semester::query()->where('name', $semester)->firstOrFail();
    }
}
