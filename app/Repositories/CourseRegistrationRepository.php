<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingCourseRegistration;
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

final class CourseRegistrationRepository
{
    public function __construct(public RegistrationService $service)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        return $this->service->getCourseRegistrationsByRegistrationNumber($registrationNumber);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByDepartmentAndSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        return $this->service->getCourseRegistrationsByDepartmentSessionAndLevel($departmentId, $session, $level);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        return $this->service->getCourseRegistrationsByDepartmentSessionAndSemester($departmentId, $session, $semester);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsBySessionAndCourse(string $session, int $courseId): Collection
    {
        return $this->service->getCourseRegistrationsBySessionAndCourse($session, $courseId);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveCourseRegistrations(Collection $registrations): Collection
    {
        $results = [];

        $studentRegistrations = $registrations->groupBy('registrationNumber');

        foreach ($studentRegistrations as $registrationNumber => $registrations) {
            try {
                $this->saveStudentCourseRegistrations($registrationNumber, $registrations);
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
    private function saveStudentCourseRegistrations(string $registrationNumber, Collection $registrations): void
    {
        $student = RegistrationNumber::new($registrationNumber)->student();

        $sessionRegistrations = $registrations->groupBy('session');

        foreach ($sessionRegistrations as $session => $registrations) {
            $session = $this->getSession($session);
            $level = $this->getLevel($registrations->firstOrFail()->level);

            $this->saveStudentSessionCourseRegistrations($student, $session, $level, $registrations);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @throws \Exception
     */
    private function saveStudentSemesterCourseRegistrations(
        Enrollment $sessionEnrollment,
        string $semester,
        Collection $registrations,
    ): void {
        $semester = $this->getSemester($semester);

        $semesterEnrollment = (new SemesterEnrollment())->firstOrCreate(
            ['enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
        );

        foreach ($registrations as $registration) {
            $this->saveCourseRegistration($semesterEnrollment, $registration);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> $registrations
     * @throws \Exception
     */
    private function saveStudentSessionCourseRegistrations(
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

            $this->saveStudentSemesterCourseRegistrations($sessionEnrollment, $semester, $registrations);
        }
    }

    /** @throws \Exception */
    private function saveCourseRegistration(
        SemesterEnrollment $semesterEnrollment,
        PortalRegistrationData $courseRegistrationData,
    ): void {

        $pendingRegistration = PendingCourseRegistration::new($semesterEnrollment, $courseRegistrationData);

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
