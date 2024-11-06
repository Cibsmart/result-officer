<?php

declare(strict_types=1);

use App\Models\ImportEvent;
use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ImportEvent::class);
            $table->string('online_id');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('other_names');
            $table->string('registration_number');
            $table->string('gender');
            $table->string('date_of_birth');
            $table->string('department_id');
            $table->string('option');
            $table->string('state');
            $table->string('local_government');
            $table->string('entry_session');
            $table->string('entry_mode');
            $table->string('entry_level');
            $table->string('phone_number');
            $table->string('email');
            $table->string('jamb_registration_number');
            $table->string('status');
            $table->text('message')->nullable();
            $table->foreignIdFor(Student::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_students');
    }
};
