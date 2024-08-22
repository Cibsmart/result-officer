<?php

declare(strict_types=1);

use App\Http\Clients\CourseClient;

it('can fetch a course by id from the api', function (): void {
    $client = new CourseClient();

    $course = $client->fetchCourseById('1');

    expect($course)->toBeArray()
        ->toHaveKeys(['id', 'course_code', 'course_title']);
})->group('external');

it('can fetch courses from the api', function (): void {
    $client = new CourseClient();

    $courses = $client->fetchCourses();

    expect($courses)->toBeArray()
        ->and($courses[0])->toBeArray()
        ->toHaveKeys(['id', 'course_code', 'course_title']);
})->group('external');

it('throws an exception for non-existent course id', function (): void {
    $client = new CourseClient();

    $client->fetchCourseById('0');

})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception for invalid course id', function (): void {
    $client = new CourseClient();

    $client->fetchCourseById('Z');
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception when connection is available', function (): void {
    Config::set('rp-http.base_url', '');

    $client = new CourseClient();

    $client->fetchCourseById('1');
})
    ->throws(Exception::class, 'ERROR CONNECTING TO API:')
    ->group('external');
