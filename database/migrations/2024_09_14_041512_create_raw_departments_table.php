<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\ImportEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ImportEvent::class);
            $table->string('online_id');
            $table->string('code');
            $table->string('name');
            $table->string('faculty');
            $table->json('options');
            $table->string('status');
            $table->text('message')->nullable();
            $table->foreignIdFor(Department::class)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_departments');
    }
};
