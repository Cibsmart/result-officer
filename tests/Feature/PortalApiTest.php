<?php

declare(strict_types=1);

it('can retrieve dept', function (): void {
    $response = Http::get('https://portal.ebsu.edu.ng/api/departments.ashx');
    //    $response = Http::get('https://result-api.test/api/departments');

    $data = $response->json()['data'];

    $output = is_string($data)
        ? json_decode($data)
        : $data;

    dd($output);
})->skip();
