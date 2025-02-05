<?php

declare(strict_types=1);

use App\Models\Level;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_session_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained();
            $table->foreignIdFor(Session::class)->constrained('academic_sessions');
            $table->foreignIdFor(Level::class)->constrained();
            $table->unsignedTinyInteger('year')->default(1);
            $table->unsignedTinyInteger('result_count')->default(0);
            $table->unsignedTinyInteger('credit_unit_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_average_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_cummulative')->default(0);
            $table->timestamps();

            $table->unique(['student_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_session_enrollments');
    }
};
