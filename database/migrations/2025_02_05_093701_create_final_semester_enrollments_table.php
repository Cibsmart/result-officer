<?php

use App\Models\FinalSessionEnrollment;
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
        Schema::create('final_semester_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FinalSessionEnrollment::class)->constrained();
            $table->foreignIdFor(Semester::class)->constrained();
            $table->unsignedTinyInteger('result_count')->default(0);
            $table->unsignedTinyInteger('credit_unit_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_sum')->default(0);
            $table->unsignedSmallInteger('grade_point_average')->default(0);
            $table->timestamps();

            $table->unique(['final_session_enrollment_id', 'semester_id'], 'unique_final_semester_enrollments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_semester_enrollments');
    }
};
