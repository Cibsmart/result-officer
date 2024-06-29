<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $courses = [
        'BIO 101' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'BIO 107' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'CHM 101' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'CHM 107' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'CSC 101' => 'INTRODUCTION TO COMPUTER SCIENCE I',
        'CSC 102' => 'INTRODUCTION TO COMPUTER SCIENCE II',
        'CSC 107' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'CSC 201' => 'INTRODUCTION TO PROGRAMMING I',
        'CSC 202' => 'INTRODUCTION TO PROGRAMMING II',
        'CSC 301' => 'STRUCTURED PROGRAMMING',
        'CSC 302' => 'FUNCTIONAL PROGRAMMING',
        'GST 101' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'GST 103' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'MTH 101' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'PHY 101' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'PHY 107' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
        'STA 131' => 'PRACTICAL SKILLS IN COMPUTER SCIENCE',
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
