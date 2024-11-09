<?php

declare(strict_types=1);

use App\Models\Level;
use App\Models\Session;
use App\Models\Student;
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
        Schema::create('session_enrollments', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained();
            $table->foreignIdFor(Session::class)->constrained('academic_sessions');
            $table->foreignIdFor(Level::class)->constrained();
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
        Schema::dropIfExists('session_enrollments');
    }
};
