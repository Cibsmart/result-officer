<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\EntryMode;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentOtherData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $state,
        public readonly string $localGovernment,
        public readonly EntryMode $entryMode,
        public readonly string $entrySession,
        public readonly string $entryLevel,
        public readonly string $jambRegistrationNumber,
        public readonly string $email,
        public readonly string $phoneNumber,
        public readonly int $entrySessionId,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $entryMode = $student->entry_mode;
        assert($entryMode instanceof EntryMode);

        $jambRegistrationNumber = $student->jamb_registration_number;
        $jambRegistrationNumber = $jambRegistrationNumber
            ? $jambRegistrationNumber
            : '';

        $email = $student->email;
        $email = $email
            ? $email
            : '';

        $phoneNumber = $student->phone_number;
        $phoneNumber = $phoneNumber
            ? $phoneNumber
            : '';

        return new self(
            id: $student->id,
            state: $student->lga->state->name,
            localGovernment: $student->lga->name,
            entryMode: $entryMode,
            entrySession: $student->entrySession->name,
            entryLevel: $student->entryLevel->name,
            jambRegistrationNumber: $jambRegistrationNumber,
            email: $email,
            phoneNumber: $phoneNumber,
            entrySessionId: $student->entry_session_id,
        );
    }
}
