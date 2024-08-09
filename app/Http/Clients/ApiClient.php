<?php

declare(strict_types=1);

namespace App\Http\Clients;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class ApiClient
{
    /**
     * @return array<int|string, string|array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function get(string $endpoint): array
    {
        try {
            /**
             * phpcs:ignore SlevomatCodingStandard.Files.LineLength
             * @var array{status: bool, message: string, data: array<int|string, string|array<string, string|array<string, string>>>} $response
             */
            $response = Http::acceptJson()
                ->baseUrl(Config::string('rp-http.base_url'))
                ->get($endpoint)
                ->throw()
                ->json();

            if (! $response['status']) {
                throw new Exception('API RETURNED ERROR: ' . $response['message']);
            }

            return $response['data'];
        } catch (ConnectionException $e) {
            $message = Str::of($e->getMessage())->before('(');

            throw new Exception('ERROR CONNECTING TO API: ' . $message);
        } catch (RequestException $e) {
            $message = Str::of($e->getMessage())->before('{');

            throw new Exception('ERROR FETCHING DATA FROM API: ' . $message);
        }
    }
}
