<?php

declare(strict_types=1);

use App\Enums\CourseStatusEnum;
use App\Enums\RecordSource;
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
        Schema::create('course_registrations', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('semester_enrollment_id')->constrained('semester_enrollments');
            $table->foreignId('course_id')->constrained('courses');
            $table->unsignedTinyInteger('credit_unit');
            $table->string('course_status', 1)->default(CourseStatusEnum::FRESH->value);
            $table->date('registration_date')->nullable();
            $table->string('online_id')->nullable();
            $table->string('source')->default(RecordSource::LEGACY->value);
            $table->timestamps();

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
