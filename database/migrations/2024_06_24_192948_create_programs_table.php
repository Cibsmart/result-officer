<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\ProgramType;
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
        Schema::create('programs', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Department::class)->constrained();
            $table->string('code');
            $table->string('name');
            $table->unsignedTinyInteger('duration')->nullable(4);
            $table->foreignIdFor(ProgramType::class)->constrained();
            $table->boolean('is_active')->default(true);
            $table->string('online_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['department_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
