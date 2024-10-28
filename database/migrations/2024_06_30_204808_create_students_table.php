<?php

declare(strict_types=1);

use App\Enums\EntryMode;
use App\Enums\RecordSource;
use App\Enums\StudentStatusEnum;
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
        Schema::create('students', static function (Blueprint $table): void {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('other_names')->nullable();
            $table->string('gender', 1);
            $table->date('date_of_birth')->nullable();
            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('entry_session_id')->constrained('academic_sessions');
            $table->string('entry_mode')->default(EntryMode::UTME->value);
            $table->foreignId('entry_level_id')->constrained('levels');
            $table->foreignId('state_id')->constrained('states');
            $table->string('local_government')->nullable();
            $table->string('jamb_registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('status')->default(StudentStatusEnum::NEW->value);
            $table->string('online_id')->nullable();
            $table->string('source')->default(RecordSource::LEGACY->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
