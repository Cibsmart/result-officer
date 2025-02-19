<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\DateValue;
use Carbon\Carbon;
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
        $dateValue = $mailDate->value;
        assert($dateValue instanceof Carbon);

        $dbMail->user_id = $user->id;
        $dbMail->title = Str::of($mailTitle)->trim()->upper()->value();
        $dbMail->date = $dateValue;

        $dbMail->save();

        return $dbMail;
    }

    /** @return array{date: 'date'} */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
