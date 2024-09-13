<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\CourseRegistrationClient;
use App\Http\Clients\ApiClient;
use Illuminate\Support\Str;

/** @phpstan-import-type CourseRegistrationDetail from \App\Data\Download\PortalCourseRegistrationData */
final readonly class FakeCourseRegistrationClient extends ApiClient implements CourseRegistrationClient
{
    public final const COURSE_REGISTRATIONS = [
        [
            'course_id' => '1',
            'credit_unit' => '3',
            'department_id' => '1',
            'id' => '1',
            'level' => '100',
            'registration_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'FIRST',
            'session' => '2009/2010',
        ],
        [
            'course_id' => '2',
            'credit_unit' => '2',
            'department_id' => '1',
            'id' => '2',
            'level' => '100',
            'registration_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'SECOND',
            'session' => '2009/2010',
        ],
        [
            'course_id' => '3',
            'credit_unit' => '3',
            'department_id' => '1',
            'id' => '3',
            'level' => '200',
            'registration_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'FIRST',
            'session' => '2010/2011',
        ],
        [
            'course_id' => '4',
            'credit_unit' => '2',
            'department_id' => '1',
            'id' => '4',
            'level' => '200',
            'registration_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'SECOND',
            'session' => '2010/2011',
        ],
        [
            'course_id' => '4',
            'credit_unit' => '2',
            'department_id' => '1',
            'id' => '4',
            'level' => '200',
            'registration_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
            'registration_number' => 'invalid/registration/number',
            'semester' => 'SECOND',
            'session' => '2010/2011',
        ],
    ];

    /** {@inheritDoc} */
    public function fetchCourseRegistrationByRegistrationNumber(string $registrationNumber): array
    {
        $registrationNumber = Str::replace('-', '/', $registrationNumber);

        $groups = ['registration_number' => $registrationNumber];

        return $this->groupCourseRegistrationBy(self::COURSE_REGISTRATIONS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array {
        $session = Str::replace('-', '/', $session);

        $groups = ['department_id' => $departmentId, 'session' => $session, 'level' => $level];

        return $this->groupCourseRegistrationBy(self::COURSE_REGISTRATIONS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array {
        $session = Str::replace('-', '/', $session);

        $groups = ['department_id' => $departmentId, 'session' => $session, 'semester' => $semester];

        return $this->groupCourseRegistrationBy(self::COURSE_REGISTRATIONS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationBySessionCourse(string $session, int $course): array
    {
        $session = Str::replace('-', '/', $session);

        $groups = ['session' => $session, 'course_id' => $course];

        return $this->groupCourseRegistrationBy(self::COURSE_REGISTRATIONS, $groups);
    }

    /**
     * @param array<int, CourseRegistrationDetail> $data
     * @param array<string, int|string> $groups
     * @return array<int, CourseRegistrationDetail>
     */
    private function groupCourseRegistrationBy(
        array $data,
        array $groups,
        int $index = 0,
    ): array {
        if (count($data) === 0 || $index === count($groups)) {
            return $data;
        }

        $keys = array_keys($groups);
        $values = array_values($groups);

        $grouped = collect($data)->groupBy($keys[$index]);

        /** @var \Illuminate\Support\Collection<int, CourseRegistrationDetail> $groupedRegistration */
        $groupedRegistration = $grouped[$values[$index]];

        /** @var array<CourseRegistrationDetail> $registrations */
        $registrations = $groupedRegistration->all();

        return $this->groupCourseRegistrationBy($registrations, $groups, $index + 1);
    }
}
