<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\Department;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Console\Command;

final class CreateImportEvents extends Command
{
    protected $signature = 'rp:create-import-events';

    protected $description = 'Create Import Events for Download of Students Registrations and Results';

    public function handle(): void
    {
        $user = User::query()->where('email', 'super@ebsu.edu.ng')->first();

        $sessions = [
            '2013-2014', '2014-2015', '2015-2016', '2016-2017', '2017-2018', '2018-2019', '2019-2020', '2020-2021',
            '2021-2022', '2022-2023', '2023-2024',
        ];

        $departments = Department::all();

        foreach ($sessions as $session) {
            foreach ($departments as $department) {
                ImportEvent::new(
                    user: $user, type: ImportEventType::REGISTRATIONS, method: ImportEventMethod::DEPARTMENT_SESSION,
                    data: [
                        'department' => $department->name, 'online_department_id' => $department->online_id,
                        'session' => $session,
                    ],
                    status: ImportEventStatus::QUEUED,
                );
            }
        }

        foreach ($sessions as $session) {
            foreach ($departments as $department) {
                ImportEvent::new(
                    user: $user, type: ImportEventType::RESULTS, method: ImportEventMethod::DEPARTMENT_SESSION,
                    data: [
                        'department' => $department->name, 'online_department_id' => $department->online_id,
                        'session' => $session,
                    ],
                    status: ImportEventStatus::QUEUED,
                );
            }
        }
    }
}
