<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalStudentData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;

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

    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status;
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
