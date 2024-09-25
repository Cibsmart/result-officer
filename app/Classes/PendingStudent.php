<?php

declare(strict_types=1);

namespace App\Classes;

use App\Data\Download\PortalStudentData;
use App\Enums\Gender;
use App\Enums\RecordSource;
use App\Enums\StudentStatusEnum;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Level;
use App\Models\Session;
use App\Models\State;
use App\Models\Student;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use Exception;

final readonly class PendingStudent
{
    public function __construct(public Student $student)
    {
    }

    /** @throws \Exception */
    public static function new(PortalStudentData $data): self
    {
        $registrationNumber = RegistrationNumber::new($data->registrationNumber);

        $student = new Student();
        $student->date_of_birth = DateValue::fromString($data->dateOfBirth)->value;
        $student->email = $data->email;
        $student->entry_level_id = self::getEntryLevelId($data->entryLevel);
        $student->entry_mode_id = self::getEntryModeId($data->entryMode);
        $student->entry_session_id = self::getSessionId($data->entrySession, $registrationNumber);
        $student->first_name = $data->firstName;
        $student->gender = Gender::from($data->gender);
        $student->jamb_registration_number = $data->jambRegistrationNumber;
        $student->last_name = $data->lastName;
        $student->local_government = $data->localGovernment;
        $student->online_id = $data->onlineId;
        $student->other_names = $data->otherNames;
        $student->phone_number = $data->phoneNumber;
        $student->program_id = self::getProgramId($data->departmentId, $data->option);
        $student->registration_number = $registrationNumber->value;
        $student->source = RecordSource::PORTAL;
        $student->state_id = self::getStateId($data->state);
        $student->status = StudentStatusEnum::NEW;

        return new self($student);
    }

    /** @throws \Exception */
    public function save(): bool
    {
        $studentExists = Student::query()->where('registration_number', $this->student->registration_number)->exists();

        if ($studentExists) {
            throw new Exception("Student's record already exists in the database");
        }

        return $this->student->save();
    }

    private static function getEntryLevelId(string $entryLevel): int
    {
        $level = Level::query()->where('name', $entryLevel)->first();

        $level ??= Level::query()->where('name', '100')->firstOrFail();

        return $level->id;
    }

    private static function getEntryModeId(string $entryMode): int
    {
        $mode = EntryMode::query()->where('code', $entryMode)->first();

        $mode ??= EntryMode::query()->where('code', 'UTME')->firstOrFail();

        return $mode->id;
    }

    private static function getSessionId(string $session, RegistrationNumber $registrationNumber): int
    {
        $dbSession = Session::query()->where('name', $session)->first();

        $dbSession ??= Session::query()
            ->where('name', $registrationNumber->session())
            ->firstOrFail();

        return $dbSession->id;
    }

    /** @throws \Exception */
    private static function getProgramId(string $onlineDepartmentId, string $programName): int
    {
        $department = Department::query()->where('online_id', $onlineDepartmentId)->first();

        if (is_null($department) && $programName === '') {
            throw new Exception('NO DEPARTMENT DETAILS: Download Department Records and Try Again');
        }

        $programName = $programName !== ''
            ? $programName
            : $department->name;

        return $department->programs()->where('name', $programName)->firstOrFail()->id;
    }

    private static function getStateId(string $state): int
    {
        $dbState = State::query()->where('name', $state)->first();

        $dbState ??= State::query()->where('name', 'EBONYI')->firstOrFail();

        return $dbState->id;
    }
}
