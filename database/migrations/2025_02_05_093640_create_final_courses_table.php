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
        Schema::create('final_courses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->string('slug')->unique();
            $table->timestamps();

            $table->unique(['code', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_courses');
    }
};
