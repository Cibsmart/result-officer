<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LocalGovernment;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class LocalGovernmentSeeder extends Seeder
{
    public function run(): void
    {
        $content = Storage::get('seeders/lga.csv');

        assert(! is_null($content));

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            /** @var array<int, string> $data */
            $data = str_getcsv($line);

            if (count($data) < 3) {
                continue;
            }

            $state = State::getUsingName($data[1]);

            LocalGovernment::query()->create([
                'name' => $data[2],
                'state_id' => $state->id,
            ]);
        }
    }
}
