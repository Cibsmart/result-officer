<?php

declare(strict_types=1);

use App\Models\Level;
use App\Models\ProgramCurriculum;
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
        Schema::create('program_curriculum_levels', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ProgramCurriculum::class)->constrained();
            $table->foreignIdFor(Level::class)->constrained();
            $table->timestamps();

            $table->unique(['program_curriculum_id', 'level_id'], 'program_curriculum_levels_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curriculum_levels');
    }
};
