<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalResultData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RawResult extends Model
{
    public static function createFromPortalResultData(PortalResultData $data, ImportEvent $event): void
    {
        $rawResult = new self();

        $rawResult->import_event_id = $event->id;
        $rawResult->online_id = $data->onlineId;
        $rawResult->course_registration_id = $data->onlineId;
        $rawResult->registration_number = $data->registrationNumber;
        $rawResult->in_course = $data->inCourseScore;
        $rawResult->exam = $data->examScore;
        $rawResult->total = $data->totalScore;
        $rawResult->grade = $data->grade;
        $rawResult->remark = '';
        $rawResult->upload_date = $data->uploadDate;
        $rawResult->exam_date = $data->examDate;
        $rawResult->lecturer_name = $data->lecturerName;
        $rawResult->lecturer_phone = $data->lecturerPhoneNumber;
        $rawResult->lecturer_email = $data->lecturerEmail;
        $rawResult->lecturer_department = $data->lecturerDepartment;
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ImportEvent, \App\Models\RawResult> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(ImportEvent::class);
    }

    /** @return array{status: 'App\Enums\RawDataStatus'} */
    protected function casts(): array
    {
        return [
            'status' => RawDataStatus::class,
        ];
    }
}
