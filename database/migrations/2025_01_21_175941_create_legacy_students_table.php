<?php

declare(strict_types=1);

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
        Schema::create('legacy_students', function (Blueprint $table): void {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('other_names')->nullable();
            $table->string('gender');
            $table->string('birth_date')->nullable();
            $table->string('program_code');
            $table->string('entry_year');
            $table->string('entry_mode');
            $table->string('entry_level');
            $table->string('local_government');
            $table->string('status');
            $table->string('jamb_registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->unsignedBigInteger('legacy_id');
            $table->string('process_status')->index();
            $table->text('message')->nullable();
            $table->foreignIdFor(Student::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_students');
    }
};
