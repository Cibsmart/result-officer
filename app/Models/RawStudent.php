<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalStudentData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class RawStudent extends Model
{
    public static function createFromPortalStudentData(PortalStudentData $data, ImportEvent $event): void
    {
        $rawStudent = new self();

        $rawStudent->import_event_id = $event->id;
        $rawStudent->online_id = $data->onlineId;
        $rawStudent->last_name = $data->lastName;
        $rawStudent->first_name = $data->firstName;
        $rawStudent->other_names = $data->otherNames;
        $rawStudent->registration_number = $data->registrationNumber;
        $rawStudent->gender = $data->gender;
        $rawStudent->date_of_birth = $data->dateOfBirth;
        $rawStudent->department_id = $data->departmentId;
        $rawStudent->option = $data->option;
        $rawStudent->state = $data->state;
        $rawStudent->local_government = $data->localGovernment;
        $rawStudent->entry_session = $data->entrySession;
        $rawStudent->entry_mode = $data->entryMode;
        $rawStudent->entry_level = $data->entryLevel;
        $rawStudent->phone_number = $data->phoneNumber;
        $rawStudent->email = $data->email;
        $rawStudent->jamb_registration_number = $data->jambRegistrationNumber;
        $rawStudent->status = RawDataStatus::PENDING;

        $rawStudent->save();
    }

    public function getRegistrationNumber(): string
    {
        $registrationNumber =
            Cache::remember("reg_number_exception.{$this->registration_number}",
                fn (?RegistrationNumberException $value) => is_null($value) ? 0 : now()->addMinutes(30),
                fn () => RegistrationNumberException::query()
                    ->where('wrong_registration_number', $this->registration_number)
                    ->first());

        return $registrationNumber === null
            ? $this->registration_number
            : $registrationNumber->correct_registration_number;
    }

    public function getProgramName(): string
    {
        $program =
            Cache::remember("program_exception.{$this->option}",
                fn (?ProgramException $value) => is_null($value) ? 0 : now()->addMinutes(30),
                fn () => ProgramException::query()
                    ->where('wrong_program_name', $this->option)
                    ->first());

        return $program === null
            ? $this->option
            : $program->correct_program_name;
    }

    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
        $this->save();
    }

    public function updateStatusAndStudent(RawDataStatus $status, Student $student): void
    {
        $this->status = $status;
        $this->student_id = $student->id;
        $this->save();
    }

    /** @return array{status: 'App\Enums\RawDataStatus'} */
    protected function casts(): array
    {
        return [
            'status' => RawDataStatus::class,
        ];
    }
}
