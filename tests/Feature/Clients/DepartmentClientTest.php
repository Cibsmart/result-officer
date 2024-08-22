<?php

declare(strict_types=1);

use App\Http\Clients\DepartmentClient;

it('can fetch a department by id from the api', function (): void {
    $client = new DepartmentClient();

    $department = $client->fetchDepartmentById('1');

    expect($department)->toBeArray()
        ->toHaveKeys(['id', 'code', 'name', 'faculty', 'options'])
        ->and($department['options'])->toBeArray();
})->group('external');

it('can fetch departments from the api', function (): void {
    $client = new DepartmentClient();

    $departments = $client->fetchDepartments();

    expect($departments)->toBeArray()
        ->and($departments[0])->toBeArray()
        ->toHaveKeys(['id', 'code', 'name', 'faculty', 'options'])
        ->and($departments[0]['options'])->toBeArray();
})->group('external');

it('throws an exception for non-existent department id', function (): void {
    $client = new DepartmentClient();

    $department = $client->fetchDepartmentById('0');

    expect($department)->toBeArray()
        ->toHaveKeys(['id', 'code', 'name', 'faculty', 'options'])
        ->and($department['options'])->toBeArray();
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception for invalid department id', function (): void {
    $client = new DepartmentClient();

    $department = $client->fetchDepartmentById('Z');

    expect($department)->toBeArray()
        ->toHaveKeys(['id', 'code', 'name', 'faculty', 'options'])
        ->and($department['options'])->toBeArray();
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');
