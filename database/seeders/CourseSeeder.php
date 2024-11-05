<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\CSVFile;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CourseSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/courses.csv'))->read();

        $courses = $content->sortBy('code');

        foreach ($courses as $course) {

            $code = Str::trim($course['code']);
            $title = Str::of($course['title'])->replace('  ', ' ')->trim()->value();

            if (Course::where('code', $code)->where('title', $title)->exists()) {
                continue;
            }

            Course::query()->create([
                'active' => true,
                'code' => $code,
                'online_id' => $course['online_id'],
                'title' => $title,
            ]);
        }
    }
}
