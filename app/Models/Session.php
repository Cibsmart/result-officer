<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\SessionValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Session extends Model
{
    protected $table = 'academic_sessions';

    protected $fillable = ['name'];

    public static function getUsingName(string $sessionName): self
    {
        $sessionName = Str::replace('/', '-', $sessionName);

        return self::query()->where('name', $sessionName)->firstOrFail();
    }

    public static function sessionFromYear(int $year): string
    {
        $next = $year + 1;

        return Str::of((string) $year)
            ->append('-')
            ->append((string) $next)
            ->value();
    }

    public function firstYear(): int
    {
        return SessionValue::new($this->name)->firstYear();
    }

    public function lastYear(): int
    {
        return SessionValue::new($this->name)->lastYear();
    }

    public function entry(Level $level): self
    {
        if ($level->name === '100') {
            return $this;
        }

        // given 2011-2012 with level->id 3 => 2009
        $entryYear = $this->firstYear() - $level->id + 1;

        $sessionName = self::sessionFromYear($entryYear);

        return self::getUsingName($sessionName);
    }
}
