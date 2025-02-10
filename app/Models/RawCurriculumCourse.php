<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class RawCurriculumCourse extends Model
{
    /**
     * @param array<string, string> $row
     * @param array<string, string> $headings
     */
    public static function fromExcelRow(
        array $row,
        ExcelImportEvent $event,
        array $headings,
    ): self {
        $curriculumCourse = new self();

        $curriculumCourse->excel_import_event_id = $event->id;
        $curriculumCourse->sn = (int) Str::trim($row[$headings['sn']]);
        $curriculumCourse->program = Str::trim($row[$headings['program']]);
        $curriculumCourse->curriculum = Str::trim($row[$headings['curriculum']]);
        $curriculumCourse->entry_mode = Str::trim($row[$headings['entry_mode']]);
        $curriculumCourse->entry_session = Str::trim($row[$headings['entry_session']]);
        $curriculumCourse->level = Str::trim($row[$headings['level']]);
        $curriculumCourse->semester = Str::trim($row[$headings['semester']]);
        $curriculumCourse->course_type = Str::trim($row[$headings['course_type']]);
        $curriculumCourse->course_code = Str::trim($row[$headings['course_code']]);
        $curriculumCourse->course_title = Str::trim($row[$headings['course_title']]);
        $curriculumCourse->credit_unit = (int) Str::trim($row[$headings['credit_unit']]);
        $curriculumCourse->minimum_elective_unit = (int) Str::trim($row[$headings['minimum_elective_unit']]);
        $curriculumCourse->minimum_elective_count = (int) Str::trim($row[$headings['minimum_elective_count']]);
        $curriculumCourse->elective_group = Str::trim($row[$headings['elective_group']]);

        $curriculumCourse->save();

        return $curriculumCourse;
    }
}
