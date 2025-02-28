<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalResultData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class RawResult extends Model
{
    public static function createFromPortalResultData(PortalResultData $data, ImportEvent $event): void
    {
        $rawResult = new self();

        $rawResult->import_event_id = $event->id;
        $rawResult->online_id = $data->onlineId;
        $rawResult->registration_id = $data->onlineId;
        $rawResult->registration_number = $data->registrationNumber;
        $rawResult->in_course = $data->inCourseScore;
        $rawResult->exam = $data->examScore;
        $rawResult->total = $data->totalScore;
        $rawResult->grade = $data->grade;
        $rawResult->remark = '';
        $rawResult->upload_date = $data->uploadDate;
        $rawResult->exam_date = $data->examDate;
        $rawResult->lecturer_name = Str::trim($data->lecturerName);
        $rawResult->lecturer_phone = Str::trim($data->lecturerPhoneNumber);
        $rawResult->lecturer_email = Str::trim($data->lecturerEmail);
        $rawResult->lecturer_department = Str::trim($data->lecturerDepartment);
        $rawResult->status = RawDataStatus::PENDING;

        $rawResult->save();
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Result, \App\Models\RawResult> */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ImportEvent, \App\Models\RawResult> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(ImportEvent::class);
    }

    public function updateStatusAndResult(RawDataStatus $status, Result $result): void
    {
        $this->status = $status;
        $this->result_id = $result->id;
        $this->save();
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

    /** @return array{status: 'App\Enums\RawDataStatus'} */
    protected function casts(): array
    {
        return [
            'status' => RawDataStatus::class,
        ];
    }
}
