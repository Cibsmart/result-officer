<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\ExamOfficer\ExamOfficerListData;

final class ExamOfficerController
{
    public function __invoke(): ExamOfficerListData
    {
        return ExamOfficerListData::new();
    }
}
