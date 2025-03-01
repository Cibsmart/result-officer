<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;

final class RawFinalResult extends Model
{
    public function updateStatus(RawDataStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function setFinalResults(FinalResult $finalResult): void
    {
        $this->final_result_id = $finalResult->id;
        $this->save();
    }
}
