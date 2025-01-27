<?php

declare(strict_types=1);

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_number_alternatives', function (Blueprint $table): void {
            $table->id();
            $table->string('wrong_registration_number')->unique('wrong_registration_number_unique_index');
            $table->string('correct_registration_number');
            $table->foreignIdFor(Student::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_number_alternatives');
    }
};
