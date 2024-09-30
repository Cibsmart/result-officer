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
        $rawDepartment = new self();
        $rawDepartment->import_event_id = $event->id;
        $rawDepartment->status = RawDataStatus::PENDING;
        $rawDepartment->online_id = $data->onlineId;
        $rawDepartment->code = $data->departmentCode;
        $rawDepartment->name = $data->departmentName;
        $rawDepartment->faculty = $data->facultyName;
        $rawDepartment->options = $data->programs->map(fn ($dept) => $dept->name)->all();
        $rawDepartment->save();
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

    /** @return array{options: 'array', status: 'App\Enums\RawDataStatus'} */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'status' => RawDataStatus::class,
        ];
    }
}
