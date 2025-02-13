<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ExcelImportEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

final class ImportEventStatusChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public ExcelImportEvent $importEvent)
    {

    }

    public function broadcastOn(): Channel
    {
        return new Channel("imports.{$this->importEvent->user_id}");
    }
}
