<?php

declare(strict_types=1);

namespace App\Events;

use App\Data\Imports\ExcelImportEventData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

final class ExcelImportStatusChanged implements ShouldBroadcast
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public ExcelImportEventData $importEventData)
    {

    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("excelImports.{$this->importEventData->userId}");
    }
}
