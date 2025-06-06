<?php

declare(strict_types=1);

use App\Models\ImportEvent;
use App\Models\Result;
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
        Schema::create('raw_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ImportEvent::class);
            $table->string('online_id')->index();
            $table->string('registration_id')->index();
            $table->string('registration_number');
            $table->string('in_course');
            $table->string('exam');
            $table->string('total');
            $table->string('grade');
            $table->string('remark')->nullable();
            $table->string('upload_date')->nullable();
            $table->string('exam_date')->nullable();
            $table->string('lecturer_name')->nullable();
            $table->string('lecturer_phone')->nullable();
            $table->string('lecturer_email')->nullable();
            $table->string('lecturer_department')->nullable();
            $table->string('status')->index();
            $table->text('message')->nullable();
            $table->foreignIdFor(Result::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_results');
    }
};
