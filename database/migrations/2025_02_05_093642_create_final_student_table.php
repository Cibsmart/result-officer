<?php

declare(strict_types=1);

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
        Schema::create('final_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Student::class)->unique()->constrained();
            $table->year('year');
            $table->string('month');
            $table->unsignedSmallInteger('result_count')->default(0);
            $table->unsignedSmallInteger('credit_unit_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_sum')->default(0);
            $table->unsignedSmallInteger('cumulative_grade_point_average_sum')->default(0);
            $table->unsignedSmallInteger('final_cumulative_grade_point_average')->default(0);
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
        Schema::dropIfExists('final_students');
    }
};
