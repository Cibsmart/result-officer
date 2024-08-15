<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\ResultClient;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final readonly class FakeResultClient implements ResultClient
{
    public final const RESULTS = [
        [
            'course_registration_id' => '1',
            'exam_score' => '21',
            'grade' => 'F',
            'id' => '1',
            'in_course' => '11',
            'registration_number' => 'EBSU/2009/51486',
            'total_score' => '32',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
        ],
        [
            'course_registration_id' => '2',
            'exam_score' => '42',
            'grade' => 'C',
            'id' => '2',
            'in_course' => '12',
            'registration_number' => 'EBSU/2009/51486',
            'total_score' => '54',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2009'],
        ],
        [
            'course_registration_id' => '3',
            'exam_score' => '53',
            'grade' => 'B',
            'id' => '3',
            'in_course' => '13',
            'registration_number' => 'EBSU/2009/51486',
            'total_score' => '66',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
        [
            'course_registration_id' => '4',
            'exam_score' => '34',
            'grade' => 'E',
            'id' => '4',
            'in_course' => '10',
            'registration_number' => 'EBSU/2009/51486',
            'total_score' => '44',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
        [
            'course_registration_id' => '5',
            'exam_score' => '0',
            'grade' => 'F',
            'id' => '5',
            'in_course' => '0',
            'registration_number' => 'invalid/registration/number',
            'total_score' => '0',
            'upload_date' => ['day' => '27', 'month' => '08', 'year' => '2011'],
        ],
    ];

    /** {@inheritDoc} */
    public function fetchResultByCourseRegistrationId(string $onlineCourseRegistrationId): array
    {
        $groups = ['course_registration_id' => $onlineCourseRegistrationId];

        return $this->groupResultBy(self::RESULTS, $groups)[0];
    }

    /** {@inheritDoc} */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array
    {
        // TODO: Implement fetchResultsByRegistrationNumber() method.
    }

    /** {@inheritDoc} */
    public function fetchResultsByDepartmentSessionLevel(
        string $onlineDepartmentId,
        string $sessionName,
        string $levelName,
    ): array {
        // TODO: Implement fetchResultsByDepartmentSessionLevel() method.
    }

    /** {@inheritDoc} */
    public function fetchResultsByDepartmentSessionSemester(
        string $onlineDepartmentId,
        string $sessionName,
        string $semesterName,
    ): array {
        // TODO: Implement fetchResultsByDepartmentSessionSemester() method.
    }

    /** {@inheritDoc} */
    public function fetchResultsBySessionCourse(string $sessionName, string $onlineCourseId): array
    {
        // TODO: Implement fetchResultsBySessionCourse() method.
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
        $groupedResults = $grouped[$values[$index]];

        /** @var array<ResultDetail> $results */
        $results = $groupedResults->all();

        return $this->groupResultBy($results, $groups, $index + 1);
    }
}
