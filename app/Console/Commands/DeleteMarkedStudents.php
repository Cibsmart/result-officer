<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class DeleteMarkedStudents extends Command
{
    protected $signature = 'rp:delete-marked-students';

    protected $description = 'Command description';

    public function handle(): void
    {
        $students = Student::query()->whereLike('number', '%XXX')->get();

        foreach ($students as $student) {
            Artisan::call('rp:delete-student', ['registrationNumber' => $student->registration_number]);
        }
    }
}
