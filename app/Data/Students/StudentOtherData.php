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
        public readonly ?string $jambRegistrationNumber,
        public readonly ?string $email,
        public readonly ?string $phoneNumber,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(
            id: $student->id,
            state: $student->lga->state->name,
            localGovernment: $student->lga->name,
            entryMode: $student->entry_mode,
            entrySession: $student->entrySession->name,
            entryLevel: $student->entryLevel->name,
            jambRegistrationNumber: $student->jamb_registration_number,
            email: $student->email,
            phoneNumber: $student->phone_number,

        );
    }
}
