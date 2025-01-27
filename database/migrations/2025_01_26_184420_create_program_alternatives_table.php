<?php

declare(strict_types=1);

use App\Models\Program;
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
        Schema::create('program_alternatives', function (Blueprint $table): void {
            $table->id();
            $table->string('wrong_program_name')->unique();
            $table->string('correct_program_name');
            $table->foreignIdFor(Program::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_alternatives');
    }
};
