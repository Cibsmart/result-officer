<?php

declare(strict_types=1);

use App\Models\VettingEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vetting_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(VettingEvent::class);
            $table->unsignedBigInteger('vettable_id');
            $table->string('vettable_type');
            $table->string('status');
            $table->text('remarks');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vetting_reports');
    }
};
