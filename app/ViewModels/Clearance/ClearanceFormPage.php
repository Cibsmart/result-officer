<?php

declare(strict_types=1);

namespace App\ViewModels\Clearance;

use App\Data\Clearance\ClearanceMonthListData;
use App\Data\Clearance\ClearanceYearListData;
use App\Data\ExamOfficer\ExamOfficerListData;
use Spatie\LaravelData\Data;

final class ClearanceFormPage extends Data
{
    public function __construct(
        public readonly ExamOfficerListData $examOfficers,
        public readonly ClearanceYearListData $years,
        public readonly ClearanceMonthListData $months,
    ) {
    }

    public static function new(): self
    {
        return new self(
            ExamOfficerListData::new(),
            ClearanceYearListData::new(),
            ClearanceMonthListData::new(),
        );
    }
}