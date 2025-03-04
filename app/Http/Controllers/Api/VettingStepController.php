<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Vetting\VettingStepListData;
use App\Models\Student;

final class VettingStepController
{
    public function __invoke(Student $student): VettingStepListData
    {
        return VettingStepListData::fromModel($student);
    }
}
