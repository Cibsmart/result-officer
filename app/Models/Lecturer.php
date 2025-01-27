<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use function PHPUnit\Framework\assertNotNull;

final class Lecturer extends Model
{
    public static function createFromRawResult(RawResult $rawResult): self
    {
        $lecturer = new self();

        $lecturerName = $rawResult->lecturer_name;
        assertNotNull($lecturerName);

        $lecturer->name = $lecturerName;
        $lecturer->phone = $rawResult->lecturer_phone;
        $lecturer->email = $rawResult->lecturer_email;
        $lecturer->department = $rawResult->lecturer_department;

        $lecturer->save();

        return $lecturer;
    }

    public static function getUsingName(string $lecturerName): ?self
    {
        return
            Cache::remember($lecturerName,
                fn (?self $value) => is_null($value) ? null : now()->addMinutes(5),
                fn () => self::query()->where('name', $lecturerName)->first());
    }

    public static function getUsingPhoneNumber(string $phoneNumber): ?self
    {
        return
            Cache::remember($phoneNumber,
                fn (?self $value) => is_null($value) ? 0 : now()->addMinutes(5),
                fn () => self::query()->where('phone', $phoneNumber)->first());
    }

    public static function getOrCreateFromRawResult(RawResult $rawResult): self
    {
        $phoneNumber = $rawResult->lecturer_phone;

        if (! is_null($phoneNumber)) {
            $lecturer = self::getUsingPhoneNumber($phoneNumber);

            if ($lecturer) {
                return $lecturer;
            }
        }

        return self::createFromRawResult($rawResult);
    }

    public static function getOrCreateFromUsingName(string $name): self
    {
        $lecturer = self::getUsingName($name);

        if ($lecturer) {
            return $lecturer;
        }

        return self::create(['name' => $name]);
    }
}
