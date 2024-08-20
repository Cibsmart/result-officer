<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Actions\SaveResults;
use App\Helpers\GetResponse;
use App\Http\Requests\Results\ResultRequest;
use App\Services\Api\ResultService;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultByRegistrationNumberController
{
    public function __construct(private ResultService $service, private SaveResults $saveResult)
    {
    }

    /** @throws \Exception */
    public function __invoke(ResultRequest $request): RedirectResponse
    {
        try {
            $results = $this->service->getResultsByRegistrationNumber($request->input('registration_number'));

            $responses = $this->saveResult->execute($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
