<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Enums\RecordSource;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\LegacyStudent;
use App\Models\Level;
use App\Models\LocalGovernment;
use App\Models\Program;
use App\Models\RawStudent;
use App\Models\Session;
use App\Models\Student;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use Carbon\Carbon;

final readonly class StudentModelData
{
    public function __construct(
        public RegistrationNumber $registrationNumber,
        public string $lastName,
        public string $firstName,
        public string $otherNames,
        public Gender $gender,
        public ?Carbon $dateOfBirth,
        public Program $program,
        public Session $entrySession,
        public Level $entryLevel,
        public EntryMode $entryMode,
        public LocalGovernment $localGovernment,
        public string $jambRegistrationNumber,
        public string $email,
        public string $phoneNumber,
        public StudentStatus $status,
        public string $onlineId,
        public RecordSource $source,
    ) {
    }

    public static function fromRawStudent(RawStudent $rawStudent): self
    {
        $registrationNumber = RegistrationNumber::new($rawStudent->getRegistrationNumber());
        $department = Department::getUsingOnlineId($rawStudent->department_id);
        $dateOfBirth = DateValue::fromValue($rawStudent->date_of_birth);

        return new self(
            registrationNumber: $registrationNumber,
            lastName: $rawStudent->last_name,
            firstName: $rawStudent->first_name,
            otherNames: $rawStudent->other_names,
            gender: Gender::from($rawStudent->gender),
            dateOfBirth: $dateOfBirth->value,
            program: Program::getFromDepartmentAndName($department, $rawStudent->getProgramName()),
            entrySession: Session::getUsingName($rawStudent->entry_session),
            entryLevel: Level::getUsingName($rawStudent->entry_level),
            entryMode: EntryMode::get($rawStudent->entry_mode),
            localGovernment: LocalGovernment::getUsingName($rawStudent->local_government),
            jambRegistrationNumber: $rawStudent->jamb_registration_number,
            email: $rawStudent->email,
            phoneNumber: $rawStudent->phone_number,
            status: StudentStatus::NEW,
            onlineId: $rawStudent->online_id,
            source: RecordSource::PORTAL,
        );
    }

    public static function fromLegacyStudent(LegacyStudent $student): self
    {
        $registrationNumber = RegistrationNumber::new($student->registration_number);
        $dateOfBirth = DateValue::fromValue($student->birth_date);

        return new self(
            registrationNumber: $registrationNumber,
            lastName: $student->last_name,
            firstName: $student->first_name,
            otherNames: $student->other_names ? $student->other_names : '',
            gender: Gender::from($student->gender),
            dateOfBirth: $dateOfBirth->value,
            program: Program::getUsingCode($student->program_code),
            entrySession: Session::getUsingName(Session::sessionFromYear((int) $student->entry_year)),
            entryLevel: Level::getUsingName($student->entry_level),
            entryMode: EntryMode::get($student->entry_mode),
            localGovernment: LocalGovernment::getUsingName($student->local_government),
            jambRegistrationNumber: $student->jamb_registration_number ? $student->jamb_registration_number : '',
            email: $student->email ? $student->email : '',
            phoneNumber: $student->phone_number ? $student->phone_number : '',
            status: StudentStatus::from($student->status),
            onlineId: '',
            source: RecordSource::LEGACY,
        );
    }

    public function getModel(): Student
    {
        $student = new Student();

        $student->registration_number = $this->registrationNumber->value;
        $student->number = $this->registrationNumber->number();
        $student->slug = $this->registrationNumber->slug();
        $student->last_name = $this->lastName;
        $student->first_name = $this->firstName;
        $student->other_names = $this->otherNames;
        $student->gender = $this->gender;
        $student->date_of_birth = $this->dateOfBirth;
        $student->program_id = $this->program->id;
        $student->local_government_id = $this->localGovernment->id;
        $student->entry_session_id = $this->entrySession->id;
        $student->entry_level_id = $this->entryLevel->id;
        $student->entry_mode = $this->entryMode;
        $student->jamb_registration_number = $this->jambRegistrationNumber;
        $student->email = $this->email;
        $student->phone_number = $this->phoneNumber;
        $student->online_id = $this->onlineId;
        $student->status = $this->status;
        $student->source = $this->source;

        return $student;
    }
}
