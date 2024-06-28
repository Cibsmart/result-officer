<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $courses = [
        'CSC 101' => 'INTRODUCTION TO COMPUTER SCIENCE I',
        'CSC 102' => 'INTRODUCTION TO COMPUTER SCIENCE I',
        'CSC 201' => 'INTRODUCTION TO PROGRAMMING I',
        'CSC 202' => 'INTRODUCTION TO PROGRAMMING II',
        'CSC 301' => 'STRUCTURED PROGRAMMING',
        'CSC 302' => 'FUNCTIONAL PROGRAMMING',
    ];

    public function run(): void
    {
        foreach ($this->courses as $code => $title) {
            Course::query()->create([
                'active' => true,
                'code' => $code,
                'title' => $title,
            ]);
        }
    }

}
