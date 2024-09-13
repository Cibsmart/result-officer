<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\ResultClient;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final readonly class FakeResultClient implements ResultClient
{
    public final const RESULTS = [
        [
            'course_id' => '1',
            'course_registration_id' => '1',
            'department_id' => '1',
            'exam_score' => '21',
            'grade' => 'F',
            'id' => '1',
            'in_course' => '11',
            'level' => '100',
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'FIRST',
            'session' => '2009/2010',
            'total_score' => '32',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
        ],
        [
            'course_id' => '2',
            'course_registration_id' => '2',
            'exam_score' => '42',
            'grade' => 'C',
            'id' => '2',
            'in_course' => '12',
            'level' => '100',
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'SECOND',
            'session' => '2009/2010',
            'total_score' => '54',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
        ],
        [
            'course_id' => '1',
            'course_registration_id' => '3',
            'exam_score' => '53',
            'grade' => 'B',
            'id' => '3',
            'in_course' => '13',
            'level' => '200',
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'FIRST',
            'session' => '2010/2011',
            'total_score' => '66',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
        [
            'course_id' => '2',
            'course_registration_id' => '4',
            'exam_score' => '34',
            'grade' => 'E',
            'id' => '4',
            'in_course' => '10',
            'level' => '200',
            'registration_number' => 'EBSU/2009/51486',
            'semester' => 'SECOND',
            'session' => '2010/2011',
            'total_score' => '44',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
        [
            'course_id' => '1',
            'course_registration_id' => '5',
            'exam_score' => '0',
            'grade' => 'F',
            'id' => '5',
            'in_course' => '0',
            'level' => '100',
            'registration_number' => 'EBSU/2009/51487',
            'semester' => 'FIRST',
            'session' => '2009/2010',
            'total_score' => '0',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
        [
            'course_id' => '1',
            'course_registration_id' => '6',
            'department_id' => '1',
            'exam_score' => '21',
            'grade' => 'F',
            'id' => '6',
            'in_course' => '11',
            'level' => '100',
            'registration_number' => 'EBSU/2009/51895',
            'semester' => 'FIRST',
            'session' => '2009/2010',
            'total_score' => '32',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
        ],
    ];

    /** {@inheritDoc} */
    public function fetchResultByCourseRegistrationId(int $courseRegistrationId): array
    {
        $groups = ['course_registration_id' => $courseRegistrationId];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array
    {
        $groups = ['registration_number' => $registrationNumber];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchResultsByRegistrationNumberSessionAndSemester(
        string $registrationNumber,
        string $session,
        string $semester,
    ): array {
        $groups = ['registration_number' => $registrationNumber, 'session' => $session, 'semester' => $semester];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchResultsByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array {
        $groups = ['department_id' => $departmentId, 'session' => $session, 'level' => $level];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchResultsByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array {
        $groups = ['department_id' => $departmentId, 'session' => $session, 'semester' => $semester];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /** {@inheritDoc} */
    public function fetchResultsBySessionCourse(string $session, int $courseId): array
    {
        $groups = ['session' => $session, 'course_id' => $courseId];

        return $this->groupResultBy(self::RESULTS, $groups);
    }

    /**
     * @param array<int, ResultDetail> $data
     * @param array<string, string> $groups
     * @return array<int, ResultDetail>
     */
    private function groupResultBy(
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

        /** @var \Illuminate\Support\Collection<int, ResultDetail> $groupedResults */
        $groupedResults = $grouped->has($values[$index])
            ? $grouped[$values[$index]]
            : collect([]);

        /** @var array<ResultDetail> $results */
        $results = $groupedResults->all();

        return $this->groupResultBy($results, $groups, $index + 1);
    }
}
