<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

final class Curriculum extends Model
{
    use Searchable;

    public static function getUsingCode(string $curriculumCode): self
    {
        return self::query()->where('code', $curriculumCode)->firstOrFail();
    }

    /** @return array<string, string|null> */
    public function toSearchableArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
