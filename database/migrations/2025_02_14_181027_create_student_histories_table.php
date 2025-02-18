<?php

declare(strict_types=1);

use App\Models\DBMail;
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
        Schema::create('student_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('modifiable_id');
            $table->string('modifiable_type');
            $table->string('action');
            $table->string('field')->nullable();
            $table->json('data')->nullable();
            $table->string('remark');
            $table->string('source');
            $table->foreignIdFor(DBMail::class, 'db_mail_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_histories');
    }
};
