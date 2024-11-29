<?php

declare(strict_types=1);

use App\Models\Semester;
use App\Models\SessionEnrollment;
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
            $table->foreignIdFor(SessionEnrollment::class)->constrained();
            $table->foreignIdFor(Semester::class)->constrained();
            $table->unsignedSmallInteger('cus')->default(0);
            $table->unsignedSmallInteger('gps')->default(0);
            $table->unsignedSmallInteger('gpa')->default(0);
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
