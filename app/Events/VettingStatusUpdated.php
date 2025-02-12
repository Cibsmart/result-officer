<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

final class VettingStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Student $student)
    {
    }

    /** @return array<int, \Illuminate\Broadcasting\Channel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("vetting.student.{$this->student->id}"),
        ];
    }
}
