<?php

declare(strict_types=1);

namespace App\Imports;

use App\Imports\Excel\SpreadsheetImport;
use App\Models\RawCurriculumCourse;

final class CurriculumCoursesImport extends SpreadsheetImport
{
    /** {@inheritDoc} */
    protected function model(): string
    {
        return RawCurriculumCourse::class;
    }

    /** {@inheritDoc} */
    protected function mapRow(array $row): ?array
    {
        if (
            ! isset($row[$this->headings['course_code']])
            || $row[$this->headings['course_code']] === ''
        ) {
            return null;
        }

        return RawCurriculumCourse::attributesFromExcelRow($row, $this->event, $this->headings);
    }
}
