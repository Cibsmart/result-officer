<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ExamOfficer extends Model
{
    public static function getOrCreate(string $examOfficerName): self
    {
        $examOfficer = self::query()->where('name', $examOfficerName)->first();

        if ($examOfficer) {
            return $examOfficer;
        }

        $examOfficer = new self();
        $examOfficer->name = $examOfficerName;
        $examOfficer->save();

        return $examOfficer;
    }
}
