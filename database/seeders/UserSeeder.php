<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /** @var array<string, array{name: string, departments: array<int, string>}> */
    private array $users = [
        'ezenkwa.maryrose@ebsu.edu.ng' => ['name' => 'EZENKWA MARYROSE', 'departments' => ['HEC', 'HKE']],
        'igwe.christiana@ebsu.edu.ng' => ['name' => 'IGWE CHRISTIANA', 'departments' => ['PHS', 'MLS', 'POL']],
        'iloke.veronica@ebsu.edu.ng' => ['name' => 'ILOKE VERONICA', 'departments' => ['CSC', 'PHY', 'ICH', 'GEX']],
        'mbam.chinyere@ebsu.edu.ng' => ['name' => 'MBAM CHINYERE', 'departments' => ['MAC', 'PSY', 'BTE', 'LIB']],
        'mbam.christiana@ebsu.edu.ng' => ['name' => 'MBAM CHRISTIANA', 'departments' => ['EDU', 'SED', 'TVE']],
        'mbam.simon@ebsu.edu.ng' => [
            'departments' => ['AEM', 'ANS', 'FAQ', 'CLM', 'FST', 'SEM'],
            'name' => 'MBAM SIMON',
        ],
        'nwali.mary@ebsu.edu.ng' => ['name' => 'NWALI MARY', 'departments' => ['AES', 'BED']],
        'nwanchor.chinwe@ebsu.edu.ng' => ['name' => 'NWANCHOR CHINWE', 'departments' => ['BCH', 'MAT']],
        'nwankwo.juliet@ebsu.edu.ng' => [
            'departments' => ['AGE', 'ARCH', 'BLD', 'CPE', 'CVE', 'EEE', 'EVM', 'EST', 'MPE', 'MME'],
            'name' => 'NWANKWO JULIET',
        ],
        'ogbuinya.blessing@ebsu.edu.ng' => ['name' => 'OGBUINYA BLESSING', 'departments' => ['ACC', 'BAF', 'SOC']],
        'ogiji.nweke@ebsu.edu.ng' => ['name' => 'OGIJI NWEKE', 'departments' => ['HIR']],
        'okemini.comfort@ebsu.edu.ng' => ['name' => 'OKEMINI COMFORT', 'departments' => ['PHILREL', 'LIN']],
        'okinya.abigail@ebsu.edu.ng' => ['name' => 'OKINYA ABIGAIL', 'departments' => ['PUB', 'MAN']],
        'okoro.ngozi@ebsu.edu.ng' => ['name' => 'OKORO NGOZI', 'departments' => ['NSC', 'MED']],
        'oseh.josephine@ebsu.edu.ng' => ['name' => 'OSEH JOSEPHINE', 'departments' => ['ENG', 'SWK']],
        'uchechukwu.rosemary@ebsu.edu.ng' => ['name' => 'UCHECHUKWU ROSEMARY', 'departments' => ['ANA', 'ECO']],
    ];

    public function run(): void
    {
        User::query()->create([
            'email' => 'oriko.blessing@ebsu.edu.ng', 'name' => 'ORIKO BLESSING',
            'password' => 'oriko.blessing@ebsu.edu.ng',
            'role' => Role::ADMIN,
        ]);

        User::query()->create([
            'email' => 'oguzor.rebecca@ebsu.edu.ng', 'name' => 'OGUZOR REBECCA',
            'password' => 'oguzor.rebecca@ebsu.edu.ng',
            'role' => Role::ADMIN,
        ]);

        foreach ($this->users as $email => $user) {
            $newUser = User::query()->create([
                'email' => $email,
                'name' => $user['name'],
                'password' => $email,
                'role' => Role::DATABASE_OFFICER,
            ]);

            foreach ($user['departments'] as $departmentCode) {
                $department = Department::query()->where('code', $departmentCode)->firstOrFail();

                $newUser->departments()->create(['department_id' => $department->id]);
            }
        }

        User::query()->create([
            'email' => 'super@ebsu.edu.ng', 'name' => 'Super Admin', 'password' => 'password',
            'role' => Role::SUPER_ADMIN,
        ]);

        User::query()->create([
            'email' => 'director@ebsu.edu.ng', 'name' => 'DIRECTOR RDB', 'password' => 'director@ebsu.edu.ng',
            'role' => Role::SUPER_ADMIN,
        ]);
    }
}
