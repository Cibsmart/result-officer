<?php

declare(strict_types=1);

use App\Models\VettingEvent;
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
        Schema::create('vetting_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(VettingEvent::class)->constrained();
            $table->string('type');
            $table->string('status');
            $table->text('message');
            $table->timestamps();

            $table->unique(['vetting_event_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vetting_steps');
    }
};
