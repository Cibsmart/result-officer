<?php

declare(strict_types=1);

namespace App\Http\Clients\Fakes;

use App\Contracts\StudentClient;
use Illuminate\Support\Str;

final class FakeStudentClient implements StudentClient
{
    public final const STUDENTS = [
        'EBSU-2009-51486' => [
            'date_of_birth' => ['day' => '27', 'month' => '08', 'year' => '1985'],
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
        'EBSU-2009-51487' => [
            'date_of_birth' => ['day' => '27', 'month' => '08', 'year' => '1985'],
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
        'EBSU-2009-51488' => [
            'date_of_birth' => ['day' => '27', 'month' => '08', 'year' => '1985'],
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
        'EBSU-2009-51895' => [
            'date_of_birth' => ['day' => '', 'month' => '', 'year' => ''],
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
        'EBSU-2010-51895' => [
            'date_of_birth' => ['day' => '', 'month' => '', 'year' => ''],
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
        'invalidRegistrationNumber' => [
            'date_of_birth' => ['day' => '', 'month' => '', 'year' => ''],
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

    /** @return array<string, string|array<string, string>> */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array
    {
        return self::STUDENTS[$registrationNumber] ?? [];
    }

    /** @return array<int, array<string, string|array<string, string>>> */
    public function fetchStudentsByDepartmentAndSession(string $departmentId, string $session): array
    {
        $session = Str::replace('-', '/', $session);

        $students = array_filter(
            self::STUDENTS,
            fn (array $student,
            ): bool => $student['department_id'] === $departmentId && $student['entry_session'] === $session,
        );

        return array_values($students);
    }

    /** @return array<int, array<string, string|array<string, string>>> */
    public function fetchStudentsBySession(string $session): array
    {
        $session = Str::replace('-', '/', $session);

        $students = array_filter(
            self::STUDENTS,
            fn (array $student): bool => $student['entry_session'] === $session,
        );

        return array_values($students);
    }
}
