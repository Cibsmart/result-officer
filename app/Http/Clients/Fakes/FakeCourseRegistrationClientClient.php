<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\CourseRegistrationClient;
use App\Http\Clients\ApiClient;
use Illuminate\Support\Str;

final class FakeCourseRegistrationClientClient extends ApiClient implements CourseRegistrationClient
{
    public final const COURSE_REGISTRATIONS = [
        [
            'course_id' => '1',
            'credit_unit' => '3',
            'department_id' => 1,
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
            'department_id' => 1,
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
            'department_id' => 1,
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
            'department_id' => 1,
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
            'department_id' => 1,
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

        $results = collect(self::COURSE_REGISTRATIONS)
            ->groupBy('registration_number');

        return $results[$registrationNumber]->all() ?? [];
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array {
        $session = Str::replace('-', '/', $session);

        $registrations = collect(self::COURSE_REGISTRATIONS)
            ->groupBy(['department_id', 'session', 'level']);

        return $registrations[$departmentId][$session][$level]->all() ?? [];
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array {
        $session = Str::replace('-', '/', $session);

        $registrations = collect(self::COURSE_REGISTRATIONS)
            ->groupBy(['department_id', 'session', 'semester']);

        return $registrations[$departmentId][$session][$semester]->all() ?? [];
    }

    /** {@inheritDoc} */
    public function fetchCourseRegistrationBySessionCourse(string $session, string $course): array
    {
        $session = Str::replace('-', '/', $session);

        $registrations = collect(self::COURSE_REGISTRATIONS)
            ->groupBy(['session', 'course_id']);

        return $registrations[$session][$course]->all() ?? [];
    }
}
