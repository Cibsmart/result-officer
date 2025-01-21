<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\CSVFile;
use App\Models\Course;
use App\Models\RawCourseAlternative;
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

            $online_id = $course['online_id'];

            Course::query()->create([
                'active' => true,
                'code' => $code,
                'online_id' => $online_id,
                'slug' => Str::slug("{$code}-{$title}"),
                'title' => $title,
            ]);

            $alternatives = $course['alternatives'];

            $alternativeIds = Str::of($alternatives)->explode('#')->filter();

            foreach ($alternativeIds as $alternativeId) {
                RawCourseAlternative::query()->create([
                    'alternative_course_id' => $online_id,
                    'original_course_id' => $alternativeId,
                ]);
            }
        }
    }
}
