<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Helpers\GetResponse;
use App\Http\Requests\Results\ResultRequest;
use App\Repositories\ResultRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultByRegistrationNumberController
{
    public function __construct(private ResultRepository $repository)
    {
    }

    /** @throws \Exception */
    public function __invoke(ResultRequest $request): RedirectResponse
    {
        try {
            $results = $this->repository->getResultByRegistrationNumber($request->input('registration_number'));

            $responses = $this->repository->saveResults($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
