<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Vetting\VettingReportListData;
use App\Models\Student;

final class VettingReportApiController
{
    public function index(Student $student): VettingReportListData
    {
        return VettingReportListData::from($student);
    }
}
