<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Actions\SaveResults;
use App\Services\Api\ResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadResultByRegistrationNumberController
{
    public function __construct(private ResultService $service, private SaveResults $saveResult)
    {
    }

    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $results = $this->service->getResultsByRegistrationNumber($request->input('registration_number'));

        $this->saveResult->execute($results);

        return back()->success('Saved');
    }
}
