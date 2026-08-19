<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class RawCurriculumCourse extends Model
{
    /**
     * @param array<string, string> $row
     * @param array<string, string> $headings
     * @return array<string, int|string|null>
     */
    public static function attributesFromExcelRow(
        array $row,
        ExcelImportEvent $event,
        array $headings,
    ): array {
        return [
            'course_code' => Str::trim($row[$headings['course_code']]),
            'course_title' => Str::trim($row[$headings['course_title']]),
            'course_type' => Str::trim($row[$headings['course_type']]),
            'credit_unit' => (int) Str::trim($row[$headings['credit_unit']]),
            'curriculum' => Str::trim($row[$headings['curriculum']]),
            'elective_group' => Str::trim($row[$headings['elective_group']]),
            'entry_mode' => Str::trim($row[$headings['entry_mode']]),
            'entry_session' => Str::trim($row[$headings['entry_session']]),
            'excel_import_event_id' => $event->id,
            'level' => Str::trim($row[$headings['level']]),
            'minimum_elective_count' => (int) Str::trim($row[$headings['minimum_elective_count']]),
            'minimum_elective_unit' => (int) Str::trim($row[$headings['minimum_elective_unit']]),
            'program' => Str::trim($row[$headings['program']]),
            'semester' => Str::trim($row[$headings['semester']]),
            'sn' => (int) Str::trim($row[$headings['sn']]),
        ];
    }

    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }
}
