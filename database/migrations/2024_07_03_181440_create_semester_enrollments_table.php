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
        Schema::create('semester_enrollments', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('session_enrollment_id')->constrained('session_enrollments');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->timestamps();

            $table->unique(['session_enrollment_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_enrollments');
    }
};
