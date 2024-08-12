<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Curriculum;
use Illuminate\Database\Seeder;

final class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        Curriculum::query()->create([
            'code' => 'BMAS',
            'name' => 'BENCHMARK MINIMUM ACADEMIC STANDARD',
        ]);

        Curriculum::query()->create([
            'code' => 'CCMAS',
            'name' => 'CORE CURRICULUM MINIMUM ACADEMIC STANDARD',
        ]);

        //        $this->call([
        //            ProgramCurriculumSeeder::class,
        //            ProgramCourseSeeder::class,
        //        ]);
    }
}
