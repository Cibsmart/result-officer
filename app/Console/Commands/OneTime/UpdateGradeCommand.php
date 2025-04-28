<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Enums\CreditUnit;
use App\Models\Result;
use App\Values\TotalScore;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class UpdateGradeCommand extends Command
{
    protected $signature = 'rp:update-grade';

    protected $description = 'Get all results greater than 39 with F grade from 2018 and correct accordingly';

    public function handle(): void
    {
        $query = $this->getQuery();

        $bar = $this->output->createProgressBar($query->count());

        $bar->start();

        foreach ($query->get() as $result) {
            $result = Result::getUsingId($result->id);

            $registration = $result->registration;
            $creditUnit = $registration->credit_unit;
            assert($creditUnit instanceof CreditUnit);

            $grade = TotalScore::new($result->total_score)->grade(true);
            $gradePoint = $grade->point() * $creditUnit->value;

            $result->update(['grade' => $grade->value, 'grade_point' => $gradePoint]);

            $result->resultDetail()->update(['value' => $result->getData()]);

            $bar->advance();
        }

        $bar->finish();
    }

    private function getQuery(): Builder
    {
        return DB::table('results as res')
            ->join('registrations as reg', 'reg.id', '=', 'res.registration_id')
            ->join('semester_enrollments as sme', 'sme.id', '=', 'reg.semester_enrollment_id')
            ->join('session_enrollments as sse', 'sse.id', '=', 'sme.session_enrollment_id')
            ->join('academic_sessions as ss', 'sse.session_id', '=', 'ss.id')
            ->select('res.id')
            ->where('res.total_score', '>=', 40)
            ->where('res.grade', '=', 'F')
            ->where('ss.id', '>', 28)
            ->orderBy('res.id');
    }
}
