<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

final class RemoveRedundantStudent extends Command
{
    protected $signature = 'rp:remove-redundant-student';

    protected $description = 'One Time Command to remove students with duplicate numbers and no results';

    public function handle(): void
    {
        $duplicated = $this->getDuplicatedRegistrationNumbers();

        $suffixes = ['U', 'V', 'W', 'X', 'Y', 'Z'];

        $key = 0;
        $previousNumber = '';

        foreach ($duplicated as $student) {
            $key = $student->number !== $previousNumber
                ? 0
                : $key + 1;

            $previousNumber = $student->number;

            $this->modifyStudentNumberWithSuffix($key, $student, $suffixes[$key]);
        }
    }

    /** @return \Illuminate\Support\Collection<int, \stdClass> */
    private function getDuplicatedRegistrationNumbers(): Collection
    {
        $uniqueDuplicateNumbers = DB::table('students as s')
            ->select('number')
            ->groupBy('number')
            ->havingRaw('count(*) > 1')
            ->get();

        $resultCounts = DB::table('students as s')
            ->leftJoin('session_enrollments as ss', 's.id', '=', 'ss.student_id')
            ->leftJoin('semester_enrollments as sm', 'ss.id', '=', 'sm.session_enrollment_id')
            ->leftJoin('registrations as r', 'sm.id', '=', 'r.semester_enrollment_id')
            ->leftJoin('results as re', 'r.id', '=', 're.registration_id')
            ->select('s.id as student_id')
            ->selectRaw('count(r.id) as result_count')
            ->whereIn('s.number', $uniqueDuplicateNumbers->pluck('number'))
            ->groupBy('s.id');

        return DB::table('students as s')
            ->joinSub($resultCounts, 'rc', function (JoinClause $join): void {
                $join->on('s.id', '=', 'rc.student_id');
            })
            ->select('s.id', 's.number', 'rc.result_count')
            ->whereIn('s.number', $uniqueDuplicateNumbers->pluck('number'))
            ->orderBy('s.number')
            ->orderBy('rc.result_count', 'desc')
            ->get();
    }

    private function modifyStudentNumberWithSuffix(
        int $key,
        stdClass $student,
        string $suffixes,
    ): void {
        if ($key === 0 && $student->result_count > 0) {
            return;
        }

        $suffix = $student->result_count === 0
            ? 'XXX'
            : $suffixes;

        Student::where('id', $student->id)
            ->update(['number' => $student->number . $suffix]);
    }
}
