<?php

declare(strict_types=1);

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
        Schema::create('program_courses', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_curriculum_id')->constrained('program_curricula');
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('credit_unit_id')->constrained('credit_units');
            $table->foreignId('course_type_id')->constrained('course_types');
            $table->timestamps();

            $table->unique(['program_curriculum_id', 'course_id']);
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
