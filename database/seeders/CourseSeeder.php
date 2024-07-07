<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $courses = [
        ['GST 101' => 'USE OF ENGLISH I'],
        ['GST 103' => 'NIGERIA PEOPLE AND CULTURE'],
        ['CSC 101' => 'INTRODUCTION TO COMPUTER SCIENCE I'],
        ['MAT 101' => 'ALGEBRA AND TRIGONOMETRY'],
        ['STA 101' => 'BASIC STATISTICS'],
        ['PHY 101' => 'GENERAL PHYSICS FOR PHYSICAL SCIENCE I'],
        ['BIO 191' => 'GENERAL PRACTICAL BIOLOGY I'],
        ['PHY 191' => 'GENERAL PRACTICAL PHYSICS I'],
        ['ICH 101' => 'GENERAL CHEMISTRY I'],
        ['BIO 101' => 'GENERAL BIOLOGY I'],
        ['ICH 191' => 'GENERAL PRACTICAL CHEMISTRY I'],

        ['GST 106' => 'SOCIAL SCIENCE'],
        ['GST 107' => 'USE OF ENGLISH II'],
        ['PHY 102' => 'GENERAL PHYSICS II'],
        ['PHY 192' => 'GENERAL PRACTICAL PHYSICS II'],
        ['ICH 102' => 'GENERAL CHEMISTRY II'],
        ['MAT 102' => 'CALCULUS AND COORDINATE GEOMETRY'],
        ['CSC 102' => 'INTRODUCTION TO COMPUTER SYSTEM'],
        ['CSC 104' => 'COMPUTER AND SOCIETY'],
        ['MAT 104' => 'VECTOR AND COORDINATE GEOMETRY'],
        ['ICH 192' => 'GENERAL PRACTICAL CHEMISTRY II'],
        ['CSC 112' => 'INTRODUCTION TO COMPUTER PROGRAMMING'],
    ];

    public function run(): void
    {
        foreach ($this->courses as $course) {
            foreach ($course as $code => $title) {
                Course::query()->create([
                    'active' => true,
                    'code' => $code,
                    'title' => $title,
                ]);
            }
        }
    }
}
