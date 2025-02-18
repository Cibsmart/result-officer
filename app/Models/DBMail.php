<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\DateValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class DBMail extends Model
{
    protected $table = 'db_mails';

    public static function createFromRecordUpdate(
        User $user,
        string $mailTitle,
        DateValue $mailDate,
    ): self {
        $dbMail = new self();

        $dbMail->user_id = $user->id;
        $dbMail->title = Str::of($mailTitle)->trim()->upper()->value();
        $dbMail->date = $mailDate->value;

        $dbMail->save();

        return $dbMail;
    }
}
