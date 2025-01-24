<?php

declare(strict_types=1);

use App\Enums\RawDataStatus;
use App\Models\Registration;
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
        Schema::create('legacy_final_results', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->string('name');
            $table->string('registration_number')->index();
            $table->unsignedTinyInteger('inc');
            $table->unsignedTinyInteger('exam');
            $table->unsignedTinyInteger('credit_unit');
            $table->string('semester')->index();
            $table->string('session')->index();
            $table->unsignedBigInteger('legacy_course_id');
            $table->string('level');
            $table->string('course_code')->index();
            $table->string('course_title');
            $table->string('examiner')->nullable();
            $table->string('exam_date')->nullable();
            $table->string('cleared_year')->nullable();
            $table->string('cleared_month')->nullable();
            $table->string('cleared_upload_date')->nullable();
            $table->string('db_officers')->nullable();
            $table->string('exam_officer')->nullable();
            $table->string('old_registration_number')->nullable();
            $table->unsignedBigInteger('legacy_id');
            $table->string('status')->default(RawDataStatus::PENDING)->index();
            $table->text('message')->nullable();
            $table->foreignIdFor(Registration::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_final_results');
    }
};
