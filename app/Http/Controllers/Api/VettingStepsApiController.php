<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Vetting\VettingStepListData;
use App\Models\Student;

final class VettingStepsApiController
{
    public function index(Student $student): VettingStepListData
    {
        return VettingStepListData::from($student);
    }
}
