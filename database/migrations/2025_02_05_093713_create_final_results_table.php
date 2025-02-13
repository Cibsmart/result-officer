<?php

declare(strict_types=1);

use App\Models\FinalCourse;
use App\Models\FinalSemesterEnrollment;
use App\Models\Lecturer;
use App\Models\Registration;
use App\Models\Session;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(FinalSemesterEnrollment::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(FinalCourse::class)->constrained();
            $table->string('course_status', 1);
            $table->unsignedTinyInteger('credit_unit');
            $table->json('scores');
            $table->unsignedTinyInteger('total_score');
            $table->string('grade', 1);
            $table->unsignedTinyInteger('grade_point');
            $table->date('exam_date')->nullable();
            $table->string('remarks')->nullable();
            $table->string('source');
            $table->foreignIdFor(Lecturer::class)->nullable();
            $table->foreignIdFor(Session::class, 'original_session_id')->nullable();
            $table->foreignIdFor(Registration::class)->nullable()->constrained();
            $table->timestamps();

            $table->unique(['final_semester_enrollment_id', 'final_course_id'], 'unique_final_semester_results');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_results');
    }
};
