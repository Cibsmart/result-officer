<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class Faculty extends Model
{
    use SoftDeletes;

    private const CODES = [
        'African Institute For Health Policy And Health Systems' => 'IHPHS',
        'Agricultural and Natural Resources Management' => 'FARM',
        'Basic Medical Science' => 'FBMS',
        'Clinical Medicine' => 'FMED',
        'Education' => 'FEDU',
        'Engineering and Environmental Science' => 'FEES',
        'ENTREPRENURSHIP' => 'ENT',
        'GENERAL STUDIES' => 'GST',
        'Health Science and Technology' => 'FHST',
        'Institute for Peace and Strategic Studies' => 'IPSS',
        'Law' => 'FLAW',
        'Management Sciences' => 'FMS',
        'Sciences' => 'FSCI',
        'Social Science' => 'FSS',
        'Social Science and Humanities' => 'FSSH',
    ];

    public static function getOrCreate(string $facultyName): self
    {
        $facultyCode = self::getCode($facultyName);

        $faculty = self::query()->where('name', $facultyName)->first();

        if ($faculty) {
            return $faculty;
        }

        $faculty = new self();
        $faculty->name = $facultyName;
        $faculty->code = $facultyCode;
        $faculty->slug = Str::slug($facultyCode);
        $faculty->save();

        return $faculty;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Department, \App\Models\Faculty> */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    private static function getCode(string $facultyName): string
    {
        if (array_key_exists($facultyName, self::CODES)) {
            return self::CODES[$facultyName];
        }

        return Str::of($facultyName)->prepend('F ')
            ->explode(' ')
            ->map(fn ($word) => $word[0])
            ->join('');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtoupper(mb_trim($value)),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtoupper(mb_trim($value)),
        );
    }
}
