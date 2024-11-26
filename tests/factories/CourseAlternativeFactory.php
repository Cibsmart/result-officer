<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\CourseAlternative;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseAlternative> */
final class CourseAlternativeFactory extends Factory
{
    protected $model = CourseAlternative::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'alternate_course_id' => CourseFactory::new(),
            'original_course_id' => CourseFactory::new(),
        ];
    }
}
