<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class RawExcelResult extends Model
{
    /** @return \Illuminate\Support\Collection<int, string> */
    public static function getUniqueRegistrationNumbers(ExcelImportEvent $event): Collection
    {
        return self::query()
            ->where('excel_import_event_id', $event->id)
            ->orderBy('registration_number')
            ->distinct()
            ->pluck('registration_number');
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawExcelResult> */
    public static function getPendingRawResults(
        ExcelImportEvent $event,
        string $registrationNumber,
    ): CollectionAlias {
        return self::query()
            ->where('excel_import_event_id', $event->id)
            ->where('registration_number', $registrationNumber)
            ->where('status', 'pending')
            ->orderBy('session')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->get();
    }

    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function setRegistrationAndResult(Registration $registration, Result $result): void
    {
        $this->result_id = $result->id;
        $this->registration_id = $registration->id;
        $this->save();
    }
}
