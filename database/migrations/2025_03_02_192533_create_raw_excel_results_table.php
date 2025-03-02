<?php

declare(strict_types=1);

use App\Enums\RawDataStatus;
use App\Models\ExcelImportEvent;
use App\Models\Registration;
use App\Models\Result;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_excel_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ExcelImportEvent::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sn');
            $table->string('name');
            $table->string('registration_number')->index();
            $table->unsignedTinyInteger('in_course');
            $table->unsignedTinyInteger('in_course_2');
            $table->unsignedTinyInteger('exam');
            $table->unsignedTinyInteger('total');
            $table->string('grade', 1)->nullable();
            $table->unsignedTinyInteger('credit_unit');
            $table->string('semester');
            $table->string('session');
            $table->string('level');
            $table->string('course_code');
            $table->string('course_title');
            $table->string('department');
            $table->string('examiner');
            $table->string('examiner_department')->nullable();
            $table->string('exam_date')->nullable();
            $table->string('status')->default(RawDataStatus::PENDING)->index();
            $table->text('message')->nullable();
            $table->foreignIdFor(Registration::class)->nullable();
            $table->foreignIdFor(Result::class)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_excel_results');
    }
};
