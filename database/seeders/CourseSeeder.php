<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\CSVFile;
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

        ['CSC 217' => 'STRUCTURED AND VISUAL PROGRAMMING I'],
        ['CSC 215' => 'SCIENTIFIC PROGRAMMING'],
        ['CSC 221' => 'COMPUTER INFORMATION TECHNOLOGY'],
        ['CSC 213' => 'INTRODUCTION TO FILE PROCESSING'],
        ['MAT 201' => 'MATHEMATICAL METHODS I'],
        ['MAT 211' => 'SET LOGIC AND ALGORITHM'],
        ['STA 201' => 'STATISTICS FOR APPLIED SCIENCES'],
        ['PHY 253' => 'MODERN PHYSICS'],
        ['GST 102' => 'PHILOSOPHY AND LOGIC'],

        ['CSC 202' => 'STANDARD SOFTWARE PROGRAMME'],
        ['CSC 226' => 'SOFTWARE ENGINEERING'],
        ['CSC 204' => 'DATABASE CREATION MANAGEMENT'],
        ['CSC 242' => 'DIGITAL DESIGNS AND LOGIC'],
        ['CSC 224' => 'INTERNET OPERATIONS'],
        ['MAT 202' => 'MATHEMATICS METHODS II'],
        ['CSC 232' => 'NUMERICAL METHODS I'],
        ['MAT 212' => 'INTRODUCTION TO REAL ANALYSIS'],
        ['PHY 252' => 'ELECTRICAL ELECTRONICS CIRCUITS'],

        ['CSC 331' => 'DATA STRUCTURE'],
        ['CSC 333' => 'COMPUTATIONAL TECHNIQUES'],
        ['CSC 343' => 'COMPUTER ARCHITECTURE'],
        ['CSC 321' => 'COMPILER CONSTRUCTION'],
        ['CSC 323' => 'OPERATING SYSTEMS I'],
        ['CSC 325' => 'SYSTEM ANALYSIS AND DESIGNS'],
        ['CSC 311' => 'ASSEMBLY LANGUAGE PROGRAMMING'],
        ['CSC 345' => 'MICRO PROCESSING AND MIRCO COMPUTER'],
        ['CSC 327' => 'OPERATIONS RESEARCH'],
        ['MAT 323' => 'DISCRETE MATHEMATICS'],
        ['STA 331' => 'INFERENCE II'],

        ['CSC 399' => 'SIWES'],

        ['CSC 421' => 'COMPUTER SIMULATION AND MODELLING'],
        ['CSC 411' => 'ORGANIZATION OF PROGRAMMING LANGUAGE'],
        ['CSC 431' => 'NUMERICAL METHODS II'],
        ['CSC 433' => 'CONTROL SOFTWARE AND INTERFACING'],
        ['GST 301' => 'ENTREPRENEURSHIP I'],
        ['CSC 435' => 'SOFTWARE DESIGN AND MANAGEMENT'],
        ['CSC 423' => 'COMPUTER NETWORK'],
        ['CSC 427' => 'COMPILER CONSTRUCTION II'],
        ['CSC 413' => 'INTERNET PROGRAMMING'],
        ['CSC 491' => 'COMPUTER SEMINAR'],
        ['CSC 401' => 'WEBSITE DEVELOPMENT'],
        ['CSC 429' => 'OPERATING SYSTEM II'],

        ['CSC 402' => 'COMPUTER CENTER MANAGEMENT'],
        ['CSC 422' => 'ARTIFICIAL INTELLIGENCE'],
        ['CSC 442' => 'COMPUTER INSTALLATION AND MAINTENANCE'],
        ['CSC 412' => 'ADVANCED PROGRAMMING LANGUAGE'],
        ['CSC 432' => 'COMPUTER GRAPHICS'],
        ['CSC 424' => 'WEBSITE AND DEVELOPMENT CODE'],
        ['CSC 498' => 'RESEARCH PROJECT'],
        ['CSC 414' => 'MICRO PROGRAMMING'],
        ['GST 302' => 'ENTREPRENEURSHIP II'],
    ];

    public function run(): void
    {

        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/courses.csv'))->read();

        $courses = $content->sortBy('code');

        foreach ($courses as $course) {
            Course::query()->create([
                'active' => true,
                'code' => $course['code'],
                'online_id' => $course['online_id'],
                'title' => $course['title'],
            ]);
        }
    }
}
