<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vetting_event_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Department::class);
            $table->string('status');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vetting_event_groups');
    }
};
