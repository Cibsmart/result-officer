<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class ExamOfficer extends Model
{
    public static function getOrCreate(string $examOfficerName): self
    {
        $examOfficer = Cache::remember("exam_officer_name.{$examOfficerName}",
            now()->addDay(),
            fn () => self::query()->where('name', $examOfficerName)->first());

        if ($examOfficer) {
            return $examOfficer;
        }

        return self::createNew($examOfficerName);
    }

    public static function createNew(string $examOfficerName): self
    {
        $examOfficer = new self();
        $examOfficer->name = $examOfficerName;
        $examOfficer->save();

        return $examOfficer;
    }
}
