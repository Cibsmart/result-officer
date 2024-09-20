<?php

declare(strict_types=1);

namespace App\Data\Download;

use Spatie\LaravelData\Data;

/**
 * @phpstan-type StudentDetail array{id: string, last_name: string, first_name: string, other_names: string,
 *     registration_number:string, gender:string, date_of_birth:string, department_id: string, option: string,
 *     state: string, local_government: string, entry_session: string, entry_mode: string, entry_level: string,
 *     jamb_registration_number: string, email: string, phone_number:string, departmentid?: string, local_governemnt?:
 *     string}
 */
final class PortalStudentData extends Data
{
    public function __construct(
        public readonly string $onlineId,
        public readonly string $lastName,
        public readonly string $firstName,
        public readonly string $otherNames,
        public readonly string $registrationNumber,
        public readonly string $gender,
        public readonly string $dateOfBirth,
        public readonly string $departmentId,
        public readonly string $option,
        public readonly string $state,
        public readonly string $localGovernment,
        public readonly string $entrySession,
        public readonly string $entryMode,
        public readonly string $entryLevel,
        public readonly string $jambRegistrationNumber,
        public readonly string $email,
        public readonly string $phoneNumber,
    ) {
    }

    /** @param StudentDetail $data */
    public static function fromArray(array $data): self
    {
        $departmentId = array_key_exists('departmentid', $data)
            ? $data['departmentid']
            : $data['department_id'];

        $localGovernment = array_key_exists('local_governemnt', $data)
            ? $data['local_governemnt']
            : $data['local_government'];

        return new self(
            onlineId: (string) $data['id'],
            lastName: $data['last_name'],
            firstName: $data['first_name'],
            otherNames: $data['other_names'],
            registrationNumber: $data['registration_number'],
            gender: $data['gender'],
            dateOfBirth: $data['date_of_birth'],
            departmentId: (string) $departmentId,
            option: $data['option'],
            state: $data['state'],
            localGovernment: $localGovernment,
            entrySession: $data['entry_session'],
            entryMode: $data['entry_mode'],
            entryLevel: $data['entry_level'],
            jambRegistrationNumber: $data['jamb_registration_number'],
            email: $data['email'],
            phoneNumber: $data['phone_number'],
        );
    }
}
