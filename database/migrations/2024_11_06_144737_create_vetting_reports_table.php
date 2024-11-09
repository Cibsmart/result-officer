<?php

declare(strict_types=1);

use App\Models\VettingStep;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vetting_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(VettingStep::class)->constrained();
            $table->unsignedBigInteger('vettable_id');
            $table->string('vettable_type');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['vetting_step_id', 'vettable_type', 'vettable_id', 'deleted_at'], 'vetting_reports_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vetting_reports');
    }
};
