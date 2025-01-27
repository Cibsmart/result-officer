<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalRegistrationData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class RawRegistration extends Model
{
    public static function createFromPortalRegistrationData(PortalRegistrationData $data, ImportEvent $event): void
    {
        $rawRegistration = new self();

        $rawRegistration->import_event_id = $event->id;
        $rawRegistration->online_id = $data->onlineId;
        $rawRegistration->registration_number = $data->registrationNumber;
        $rawRegistration->session = $data->session;
        $rawRegistration->semester = $data->semester;
        $rawRegistration->level = $data->level;
        $rawRegistration->course_id = $data->courseId;
        $rawRegistration->course_title = '';
        $rawRegistration->credit_unit = $data->creditUnit;
        $rawRegistration->registration_date = $data->registrationDate;
        $rawRegistration->status = RawDataStatus::PENDING;

        $rawRegistration->save();
    }

    public function getRegistrationNumber(): string
    {
        $registrationNumber =
            Cache::remember("reg_number_exception.{$this->registration_number}",
                fn (?RegistrationNumberAlternative $value) => is_null($value) ? 0 : now()->addMinutes(30),
                fn () => RegistrationNumberAlternative::query()
                    ->where('wrong_registration_number', $this->registration_number)
                    ->first());

        return $registrationNumber === null
            ? $this->registration_number
            : $registrationNumber->correct_registration_number;
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

    public function updateStatusAndRegistration(RawDataStatus $status, Registration $registration): void
    {
        $this->status = $status;
        $this->registration_id = $registration->id;
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
