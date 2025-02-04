<?php

declare(strict_types=1);

use App\Models\ProgramCurriculumSemester;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_curriculum_semesters', function (Blueprint $table): void {
            $table->tinyInteger('maximum_credit_units')->default(24)->after('minimum_elective_units');
            $table->tinyInteger('minimum_credit_units')->default(15)->after('minimum_elective_units');
        });

        Schema::table('semester_enrollments', function (Blueprint $table): void {
            $table->foreignIdFor(ProgramCurriculumSemester::class)->nullable()->after('gpa')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('program_curriculum_semesters', function (Blueprint $table): void {
            $table->dropColumn(['minimum_credit_units', 'maximum_credit_units']);
        });

        Schema::table('semester_enrollments', function (Blueprint $table): void {
            $table->dropForeign(['program_curriculum_semester_id']);
            $table->dropColumn('program_curriculum_semester_id');
        });
    }
};
