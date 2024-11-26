<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\ProgramCurriculumCourse;
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
        Schema::create('course_alternatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Course::class, 'original_course_id')->constrained();
            $table->foreignIdFor(Course::class, 'alternate_course_id')->constrained();
            $table->foreignIdFor(ProgramCurriculumCourse::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_alternatives');
    }
};
