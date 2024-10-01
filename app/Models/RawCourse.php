<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalCourseData;
use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;

final class RawCourse extends Model
{
    public static function createFromPortalCourseData(PortalCourseData $data, ImportEvent $event): void
    {
        $rawCourse = new self();
        $rawCourse->import_event_id = $event->id;
        $rawCourse->status = RawDataStatus::PENDING;
        $rawCourse->online_id = $data->onlineId;
        $rawCourse->code = $data->code;
        $rawCourse->title = $data->title;
        $rawCourse->save();
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

    /** @return array{status: 'App\Enums\RawDataStatus'} */
    protected function casts(): array
    {
        return [
            'status' => RawDataStatus::class,
        ];
    }
}
