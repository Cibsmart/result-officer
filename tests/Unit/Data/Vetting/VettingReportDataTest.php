<?php

declare(strict_types=1);

use App\Data\Vetting\VettingReportData;
use Tests\Factories\VettingReportFactory;

covers(VettingReportData::class)

it('returns correct vetting report data', function (): void {
    $report = VettingReportFactory::new()->createOne();

    $data = VettingReportData::fromModel($report);

    expect($data)->toBeInstanceOf(VettingReportData::class)
        ->toHaveProperties(['id', 'vettableId', 'vettableType', 'message']);
});
