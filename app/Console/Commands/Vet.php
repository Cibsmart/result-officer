<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\VettingEvent;
use App\Services\Vetting\Vetting;
use Illuminate\Console\Command;

final class Vet extends Command
{
    protected $signature = 'app:vet {studentId}';

    protected $description = "Initiate Vetting of Student's Results";

    public function handle(Vetting $vetting): void
    {
        $student = Student::query()->findOrFail($this->argument('studentId'));

        $vettingEvent = VettingEvent::getOrCreateUsingStudent($student);

        $vetting->vet($vettingEvent);
    }
}
