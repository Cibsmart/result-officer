<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\ProgramCurriculumSemester;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_curriculum_courses', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ProgramCurriculumSemester::class)->constrained();
            $table->foreignIdFor(Course::class)->constrained();
            $table->unsignedSmallInteger('credit_unit');
            $table->string('course_type');
            $table->timestamps();

            $table->unique(['program_curriculum_semester_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curriculum_courses');
    }
};
