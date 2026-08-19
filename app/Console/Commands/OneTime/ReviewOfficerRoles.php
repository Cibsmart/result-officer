<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Remediation aid for the Role::creatable() label swap.
 *
 * The role dropdown mapped desk-officer to 'EXAM OFFICER' and exam-officer to
 * 'DESK OFFICER', so any account created through it holds the opposite of what
 * the operator selected. Nothing records which accounts came through that form,
 * so this reports every holder of the two affected roles for manual review
 * rather than guessing at intent and rewriting rows.
 */
final class ReviewOfficerRoles extends Command
{
    protected $signature = 'rp:review-officer-roles';

    protected $description = 'List desk-officer and exam-officer accounts for review after the Role::creatable() label swap';

    public function __invoke(): int
    {
        $rows = self::rows();

        if ($rows === []) {
            $this->info('No desk-officer or exam-officer accounts found. Nothing to review.');

            return Command::SUCCESS;
        }

        $this->warn(count($rows) . ' account(s) hold a role affected by the label swap.');
        $this->line('Each may hold the opposite of what the operator selected. Verify against the appointment record.');
        $this->newLine();

        $this->table(
            ['ID', 'Name', 'Email', 'Stored role', 'Operator likely picked', 'Created'],
            $rows,
        );

        return Command::SUCCESS;
    }

    /** @return list<array{0: int, 1: string, 2: string, 3: string, 4: string, 5: string}> */
    private static function rows(): array
    {
        $rows = [];

        $users = User::query()
            ->whereIn('role', [Role::DESK_OFFICER, Role::EXAM_OFFICER])
            ->orderBy('created_at')
            ->cursor();

        foreach ($users as $user) {
            $createdAt = $user->created_at;
            $role = $user->role;
            assert($role instanceof Role);

            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $role->getLabel(),
                self::opposite($role)->getLabel(),
                $createdAt === null ? '—' : $createdAt->toDateString(),
            ];
        }

        return $rows;
    }

    private static function opposite(Role $role): Role
    {
        return $role === Role::DESK_OFFICER
            ? Role::EXAM_OFFICER
            : Role::DESK_OFFICER;
    }
}
