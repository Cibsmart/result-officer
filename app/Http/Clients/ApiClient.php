<?php

declare(strict_types=1);

namespace App\Http\Clients;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract readonly class ApiClient
{
    /**
     * @param array<string, string|int> $parameters
     * @return array<int|string, string|array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function get(string $endpoint, array $parameters = []): array
    {
        $sanitizedParameters = array_map(fn ($value) => Str::replace('/', '-', $value), $parameters);

        try {
            /**
             * phpcs:ignore SlevomatCodingStandard.Files.LineLength
             * @var array{status?: bool, message?: string, data?: array<int|string, string|array<string, string|array<string, string>>>} $response
             */
            $response = Http::acceptJson()
                ->baseUrl(Config::string('rp_http.base_url'))
                ->get($endpoint, $sanitizedParameters)
                ->throw()
                ->json();

            if (is_null($response) || count($response) === 0) {
                throw new Exception('API RETURNED EMPTY RESPONSE');
            }

            if (! $response['status']) {
                throw new Exception('API RETURNED ERROR: ' . $response['message']);
            }

            return array_map('array_change_key_case', $response['data']);
        } catch (ConnectionException $e) {
            throw new Exception("ERROR CONNECTING TO API: {$e->getMessage()}");
        } catch (RequestException $e) {
            throw new Exception("ERROR FETCHING DATA FROM API: {$e->getMessage()}");
        }
    }
}
