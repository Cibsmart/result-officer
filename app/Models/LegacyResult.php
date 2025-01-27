<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;

final class LegacyResult extends Model
{
    protected $guarded = ['id'];

    public function updateSuccess(Registration $registration): void
    {
        $this->registration_id = $registration->id;
        $this->status = RawDataStatus::PROCESSED->value;
        $this->save();
    }

    public function updateFailure(string $message): void
    {
        $this->message = $message;
        $this->status = RawDataStatus::FAILED->value;
        $this->save();
    }
}
