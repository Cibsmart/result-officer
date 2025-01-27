<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\RecordSource;
use App\Models\Course;
use App\Models\ProgramCurriculumCourse;
use App\Models\SemesterEnrollment;
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
        Schema::create('registrations', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(SemesterEnrollment::class)->constrained();
            $table->foreignIdFor(Course::class)->constrained();
            $table->unsignedTinyInteger('credit_unit');
            $table->string('course_status', 1)->default(CourseStatus::FRESH->value);
            $table->date('registration_date')->nullable();
            $table->string('online_id')->nullable()->index();
            $table->string('source')->default(RecordSource::LEGACY->value);
            $table->foreignIdFor(ProgramCurriculumCourse::class)->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['semester_enrollment_id', 'course_id'], 'semester_enrollment_course_id');
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
