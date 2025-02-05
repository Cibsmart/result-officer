<?php

use App\Models\ExamOfficer;
use App\Models\Student;
use App\Models\User;
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
        Schema::create('student_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained();
            $table->year('year');
            $table->string('month');
            $table->unsignedTinyInteger('result_count')->default(0);
            $table->unsignedTinyInteger('credit_unit_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_sum')->default(0);
            $table->unsignedSmallInteger('cummulative_grade_point_average_sum')->default(0);
            $table->unsignedSmallInteger('final_cummulative_grade_point_average')->default(0);
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(ExamOfficer::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_clearances');
    }
};
