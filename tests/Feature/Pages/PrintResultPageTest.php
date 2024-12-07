<?php

declare(strict_types=1);

use App\Enums\ClassOfDegree;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

test('view result form loads', function (): void {
    $user = UserFactory::new()->createOne();
    $student = createStudentWithResults();

    $fcpga = computeFCGPA($student);
    $degreeClass = ClassOfDegree::for($fcpga);
    $summary = "CURRENT FINAL CGPA: $fcpga ($degreeClass->value)";

    actingAs($user)
        ->get(route('results.print', ['student' => $student]))
        ->assertStatus(200)
        ->assertViewIs('pdfs.results.view')
        ->assertSeeText($student->registration_number)
        ->assertSeeText($student->last_name)
        ->assertSeeText($student->first_name)
        ->assertSeeText($student->program->department->name)
        ->assertSeeText($summary);
});
