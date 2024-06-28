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
        Schema::create('program_curricula', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('curriculum_id')->constrained('curricula');
            $table->foreignId('level_id')->constrained('levels');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->unsignedSmallInteger('minimum_elective_units')->default(0);
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curricula');
    }
};
