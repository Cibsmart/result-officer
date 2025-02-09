<?php

declare(strict_types=1);

use App\Enums\RawDataStatus;
use App\Models\ExcelImportEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_program_curricula', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ExcelImportEvent::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sn');
            $table->string('program');
            $table->string('curriculum');
            $table->string('entry_mode');
            $table->string('entry_session');
            $table->string('level');
            $table->string('semester');
            $table->unsignedTinyInteger('minimum_elective_unit');
            $table->unsignedTinyInteger('minimum_elective_count');
            $table->unsignedTinyInteger('elective_group');
            $table->unsignedTinyInteger('course_code');
            $table->unsignedTinyInteger('course_title');
            $table->unsignedTinyInteger('credit_unit');
            $table->string('status')->default(RawDataStatus::PENDING)->index();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_program_curricula');
    }
};
