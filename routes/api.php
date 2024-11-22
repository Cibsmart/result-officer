<?php

declare(strict_types=1);

use App\Http\Controllers\Api\VettingReportApiController;

Route::get('vetting-reports/{student}', [VettingReportApiController::class, 'index']);
