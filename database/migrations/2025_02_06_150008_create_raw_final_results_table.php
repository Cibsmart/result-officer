<?php

declare(strict_types=1);

use App\Enums\RawDataStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_final_results', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('sn');
            $table->string('name');
            $table->string('registration_number')->index();
            $table->unsignedTinyInteger('in_course');
            $table->unsignedTinyInteger('exam');
            $table->unsignedTinyInteger('total');
            $table->string('grade', 1);
            $table->unsignedTinyInteger('credit_unit');
            $table->string('semester');
            $table->string('session');
            $table->string('course_code');
            $table->string('course_title');
            $table->string('department');
            $table->string('examiner');
            $table->string('examiner_department');
            $table->string('exam_date')->nullable();
            $table->year('year');
            $table->string('month');
            $table->string('originating_session');
            $table->string('database_officer');
            $table->string('exam_officer');
            $table->string('old_registration_number')->nullable();
            $table->string('status')->default(RawDataStatus::PENDING);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_final_results');
    }
};
