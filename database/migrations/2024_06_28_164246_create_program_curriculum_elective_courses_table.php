<?php

declare(strict_types=1);

use App\Models\ProgramCurriculumCourse;
use App\Models\ProgramCurriculumElectiveGroup;
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
        Schema::create('program_curriculum_elective_courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ProgramCurriculumElectiveGroup::class)
                ->constrained(indexName: 'elective_courses_elective_group_foreign_id');
            $table->foreignIdFor(ProgramCurriculumCourse::class)
                ->unique(indexName: 'elective_courses_program_course_unique')
                ->constrained(indexName: 'elective_courses_program_course_foreign_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curriculum_elective_courses');
    }
};
