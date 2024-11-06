<?php

declare(strict_types=1);

use App\Models\ImportEvent;
use App\Models\Registration;
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
        Schema::create('raw_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ImportEvent::class);
            $table->string('online_id')->index();
            $table->string('registration_number');
            $table->string('session');
            $table->string('semester');
            $table->string('level');
            $table->string('course_id');
            $table->string('course_title');
            $table->string('credit_unit');
            $table->string('registration_date')->nullable();
            $table->string('status');
            $table->text('message')->nullable();
            $table->foreignIdFor(Registration::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_registrations');
    }
};
