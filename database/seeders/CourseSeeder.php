<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\CSVFile;
use App\Models\Course;
use App\Models\LegacyCourseAlternatives;
use App\Models\RawCourseAlternative;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CourseSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/courses.csv'))->read();

        foreach ($content as $course) {
            $code = Str::trim($course['code']);
            $title = Str::of($course['title'])->replace('  ', ' ')->trim()->value();

            if (Course::where('code', $code)->where('title', $title)->exists()) {
                continue;
            }

            $onlineId = $course['online_id'];

            $dbCourse = Course::query()->create([
                'active' => true,
                'code' => $code,
                'online_id' => $onlineId,
                'slug' => Str::slug("{$code}-{$title}"),
                'title' => $title,
            ]);

            $this->processOnlineAlternatives($course['alternatives'], $onlineId);

            $this->processLegacyAlternatives($course['legacy_alternatives'], $dbCourse->id);
        }
    }

    private function processOnlineAlternatives(?string $alternatives, ?string $onlineId): void
    {
        if ($alternatives === null || $alternatives === '' || $onlineId === null) {
            return;
        }

        $alternativeIds = Str::of($alternatives)->explode('#')->filter();

        foreach ($alternativeIds as $alternativeId) {
            RawCourseAlternative::query()->create([
                'alternative_course_id' => $onlineId,
                'original_course_id' => $alternativeId,
            ]);
        }
    }

    private function processLegacyAlternatives(?string $legacyAlternatives, int $dbCourseId): void
    {
        if ($legacyAlternatives === '' || $legacyAlternatives === null) {
            return;
        }

        $alternativeLegacyIds = Str::of($legacyAlternatives)->explode('#')->filter();

        foreach ($alternativeLegacyIds as $alternativeLegacyId) {
            LegacyCourseAlternatives::query()->create([
                'alternative_course_id' => $dbCourseId,
                'original_course_id' => $alternativeLegacyId,
            ]);
        }
    }
}
