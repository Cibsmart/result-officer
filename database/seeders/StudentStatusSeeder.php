<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\StudentStatusEnum;
use App\Models\StudentStatus;
use Illuminate\Database\Seeder;

final class StudentStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (StudentStatusEnum::cases() as $status) {
            StudentStatus::query()->create([
                'name' => $status,
            ]);
        }
    }
}
