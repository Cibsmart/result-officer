<?php

declare(strict_types=1);

use App\Enums\EntryMode;
use App\Enums\RecordSource;
use App\Enums\StudentStatus;
use App\Models\Level;
use App\Models\LocalGovernment;
use App\Models\Program;
use App\Models\Session;
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
            $table->foreignIdFor(Program::class)->constrained();
            $table->foreignIdFor(Session::class, 'entry_session_id')->constrained('academic_sessions');
            $table->string('entry_mode')->default(EntryMode::UTME->value);
            $table->foreignIdFor(Level::class, 'entry_level_id')->constrained();
            $table->foreignIdFor(LocalGovernment::class)->constrained();
            $table->string('jamb_registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('status')->default(StudentStatus::NEW->value);
            $table->string('online_id')->nullable();
            $table->string('source')->default(RecordSource::LEGACY->value);
            $table->unsignedSmallInteger('fcgpa')->default(0);
            $table->timestamps();
            $table->softDeletes();
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
