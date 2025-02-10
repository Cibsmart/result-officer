<?php

declare(strict_types=1);

namespace App\Actions\Imports\Excel;

use App\Models\ExcelImportEvent;

final class ProcessRawCurriculumCourses
{
    public static function new(): self
    {
        return new self();
    }

    public function execute(ExcelImportEvent $event): void
    {
        dd('Here');
    }
}
