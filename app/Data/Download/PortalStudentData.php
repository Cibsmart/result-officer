<?php

declare(strict_types=1);

namespace App\Data\Download;

use Spatie\LaravelData\Data;

/**
 * @phpstan-type StudentDetail array{id: string, last_name: string, first_name: string, other_names: string,
 *     registration_number: string, gender: string, date_of_birth: array{day: string, month: string, year: string},
 *     department_id: string, option: string, state: string, local_government: string, entry_session: string,
 *     entry_mode: string, entry_level: string, jamb_registration_number: string, email: string, phone_number: string}
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
        public readonly PortalDateData $dateOfBirth,
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
        return new self(
            onlineId: (string) $data['id'],
            lastName: $data['last_name'],
            firstName: $data['first_name'],
            otherNames: $data['other_names'],
            registrationNumber: $data['registration_number'],
            gender: $data['gender'],
            dateOfBirth: PortalDateData::from($data['date_of_birth']),
            departmentId: (string) $data['department_id'],
            option: $data['option'],
            state: $data['state'],
            localGovernment: $data['local_government'],
            entrySession: $data['entry_session'],
            entryMode: $data['entry_mode'],
            entryLevel: $data['entry_level'],
            jambRegistrationNumber: $data['jamb_registration_number'],
            email: $data['email'],
            phoneNumber: $data['phone_number'],
        );
    }
}
