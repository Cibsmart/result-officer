<?php

declare(strict_types=1);

use App\Models\User;
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
        Schema::create('import_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('type');
            $table->string('method');
            $table->json('data');
            $table->unsignedBigInteger('students')->default(0);
            $table->unsignedBigInteger('downloaded')->default(0);
            $table->unsignedBigInteger('saved')->default(0);
            $table->unsignedBigInteger('duplicate')->default(0);
            $table->unsignedBigInteger('processed')->default(0);
            $table->unsignedBigInteger('failed')->default(0);
            $table->unsignedBigInteger('pending')->default(0);
            $table->string('status');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_import_events');
    }
};
