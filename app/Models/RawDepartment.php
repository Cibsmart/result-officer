<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalDepartmentData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;

final class RawDepartment extends Model
{
    public static function createFromPortalDepartmentData(PortalDepartmentData $data, ImportEvent $event): void
    {
        $rawCourse = new self();
        $rawCourse->import_event_id = $event->id;
        $rawCourse->status = RawDataStatus::PENDING->value;
        $rawCourse->online_id = $data->onlineId;
        $rawCourse->code = $data->departmentCode;
        $rawCourse->name = $data->departmentName;
        $rawCourse->faculty = $data->facultyName;
        $rawCourse->options = $data->programs;
        $rawCourse->save();
    }

    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status->value;
        $this->save();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
        $this->save();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }
}
