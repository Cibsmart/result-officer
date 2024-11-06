<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    public static function createFromRawResult(RawResult $rawResult): self
    {
        $lecturer = new self();

        $lecturer->name = $rawResult->lecturer_name;
        $lecturer->phone = $rawResult->lecturer_phone;
        $lecturer->email = $rawResult->lecturer_email;
        $lecturer->department = $rawResult->lecturer_department;

        $lecturer->save();

        return $lecturer;
    }

    public static function getUsingName(string $lecturerName): ?self
    {
        return self::query()->where('name', $lecturerName)->first();
    }

    public static function getUsingPhoneNumber(string $phoneNumber): ?self
    {
        return self::query()->where('phone', $phoneNumber)->first();
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
}
