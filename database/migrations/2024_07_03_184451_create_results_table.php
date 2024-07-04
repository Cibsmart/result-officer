<?php

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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('credit_unit_id')->constrained('credit_units');
            $table->foreignId('course_status_id')->constrained('course_statuses');
            $table->json('scores')->nullable();
            $table->unsignedTinyInteger('total_score')->default(0);
            $table->tinyText('grade')->nullable();
            $table->unsignedTinyInteger('grade_point')->default(0);
            $table->string('remarks')->nullable();
            $table->string('data');
            $table->string('online_id')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'semester_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
