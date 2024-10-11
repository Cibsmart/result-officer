<?php

declare(strict_types=1);

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
        Schema::create('enrollments', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('session_id')->constrained('academic_sessions');
            $table->foreignId('level_id')->constrained('levels');
            $table->unsignedSmallInteger('year')->default(1);
            $table->timestamps();

            $table->unique(['student_id', 'session_id']);
            $table->unique(['student_id', 'session_id', 'level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
