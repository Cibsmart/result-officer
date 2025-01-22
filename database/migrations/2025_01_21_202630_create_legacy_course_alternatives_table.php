<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_course_alternatives', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('original_course_id')->unique();
            $table->unsignedBigInteger('alternative_course_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_course_alternatives');
    }
};
