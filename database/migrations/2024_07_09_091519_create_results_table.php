<?php

declare(strict_types=1);

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
        Schema::create('results', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_registration_id')->constrained('course_registrations');
            $table->json('scores');
            $table->unsignedTinyInteger('total_score')->default(0);
            $table->string('grade', 1)->default('F');
            $table->unsignedTinyInteger('grade_point')->default(0);
            $table->date('upload_date')->nullable();
            $table->string('remarks')->nullable();
            $table->date('exam_date')->nullable();
            $table->string('lecturer_name')->nullable();
            $table->string('lecturer_department')->nullable();
            $table->string('data');
            $table->string('source')->default(RecordSource::LEGACY->value);
            $table->timestamps();

            $table->unique(['course_registration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
