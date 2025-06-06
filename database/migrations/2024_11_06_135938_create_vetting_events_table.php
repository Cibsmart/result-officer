<?php

declare(strict_types=1);

use App\Models\ProgramCurriculum;
use App\Models\Student;
use App\Models\User;
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
        Schema::create('vetting_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Student::class)->constrained();
            $table->foreignIdFor(ProgramCurriculum::class)->nullable()->constrained();
            $table->string('status');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->unique(['student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vetting_events');
    }
};
