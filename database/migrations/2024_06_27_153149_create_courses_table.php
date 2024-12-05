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
        Schema::create('courses', static function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('active')->default(true);
            $table->string('online_id')->nullable();
            $table->timestamps();

            $table->unique(['code', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
