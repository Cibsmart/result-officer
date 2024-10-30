<?php

use App\Models\ProgramCurriculumLevel;
use App\Models\Semester;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_curriculum_semesters', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ProgramCurriculumLevel::class)->constrained();
            $table->foreignIdFor(Semester::class)->constrained();
            $table->unsignedSmallInteger('minimum_elective_units')->default(0);
            $table->timestamps();

            $table->unique(['program_curriculum_level_id', 'semester_id'], 'program_curriculum_semesters_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curriculum_semesters');
    }
};
