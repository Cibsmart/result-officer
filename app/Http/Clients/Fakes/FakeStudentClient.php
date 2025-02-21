<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\StudentClient;

/** @phpstan-import-type StudentDetail from \App\Contracts\StudentClient */
final class FakeStudentClient implements StudentClient
{
    final public const STUDENTS = [
        [
            'date_of_birth' => '27-08-1985',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '100',
            'entry_mode' => 'UTME',
            'entry_session' => '2009/2010',
            'first_name' => 'AGBO',
            'gender' => 'M',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'UGO',
            'local_government' => '',
            'option' => '',
            'other_names' => 'CHIDI',
            'phone_number' => '',
            'registration_number' => 'EBSU/2009/51486',
            'state' => 'ANAMBRA',
        ],
        [
            'date_of_birth' => '27-08-1985',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '100',
            'entry_mode' => 'UTME',
            'entry_session' => '',
            'first_name' => 'AGBO',
            'gender' => 'M',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'UGO',
            'local_government' => '',
            'option' => '',
            'other_names' => 'CHIDI',
            'phone_number' => '',
            'registration_number' => 'EBSU/2009/51487',
            'state' => 'ANAMBRA',
        ],
        [
            'date_of_birth' => '27-08-1985',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '',
            'entry_mode' => '',
            'entry_session' => '2009/2010',
            'first_name' => 'AGBO',
            'gender' => 'M',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'UGO',
            'local_government' => '',
            'option' => '',
            'other_names' => 'CHIDI',
            'phone_number' => '',
            'registration_number' => 'EBSU/2009/51488',
            'state' => '',
        ],
        [
            'date_of_birth' => '',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '100',
            'entry_mode' => 'DE',
            'entry_session' => '2009/2010',
            'first_name' => 'IFEANYICHUKWU',
            'gender' => 'M',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'OBAZI',
            'local_government' => 'ABAKALIKI',
            'option' => '',
            'other_names' => 'OBAZI',
            'phone_number' => '07030000000',
            'registration_number' => 'EBSU/2009/51895',
            'state' => 'EBONYI',
        ],
        [
            'date_of_birth' => '',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '100',
            'entry_mode' => 'UTME',
            'entry_session' => '2009/2010',
            'first_name' => 'IFEANYICHUKWU',
            'gender' => 'Z',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'OBAZI',
            'local_government' => '',
            'option' => '',
            'other_names' => 'OBAZI',
            'phone_number' => '07030000000',
            'registration_number' => 'EBSU/2010/51895',
            'state' => '',
        ],
        [
            'date_of_birth' => '',
            'department_id' => '1',
            'email' => '',
            'entry_level' => '100',
            'entry_mode' => 'UTME',
            'entry_session' => '2009/2010',
            'first_name' => 'IFEANYICHUKWU',
            'gender' => 'M',
            'id' => '33189',
            'jamb_registration_number' => 'JAMBEBSU/2009/51471',
            'last_name' => 'OBAZI',
            'local_government' => '',
            'option' => '',
            'other_names' => 'OBAZI',
            'phone_number' => '07030000000',
            'registration_number' => 'invalidRegistrationNumber',
            'state' => '',
        ],
    ];

    /** @return array<int, array<string, string>> */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array
    {
        $groups = ['registration_number' => $registrationNumber];

        return $this->groupStudentsBy(self::STUDENTS, $groups);
    }

    /** @return array<int, array<string, string>> */
    public function fetchStudentsByDepartmentAndSession(int $departmentId, string $session): array
    {
        $groups = ['department_id' => $departmentId, 'entry_session' => $session];

        return $this->groupStudentsBy(self::STUDENTS, $groups);
    }

    /** @return array<int, array<string, string>> */
    public function fetchStudentsBySession(string $session): array
    {
        $groups = ['entry_session' => $session];

        return $this->groupStudentsBy(self::STUDENTS, $groups);
    }

    /**
     * @param array<int, StudentDetail> $data
     * @param array<string, int|string> $groups
     * @return array<int, StudentDetail>
     */
    private function groupStudentsBy(
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

        /** @var \Illuminate\Support\Collection<int, StudentDetail> $groupedStudents */
        $groupedStudents = $grouped->has($values[$index])
            ? $grouped[$values[$index]]
            : collect([]);

        /** @var array<StudentDetail> $students */
        $students = $groupedStudents->all();

        return $this->groupStudentsBy($students, $groups, $index + 1);
    }
}
