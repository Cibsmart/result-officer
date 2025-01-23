<?php

declare(strict_types=1);

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
        Schema::table('legacy_students', function (Blueprint $table): void {
            $table->text('message')->after('process_status')->nullable();
        });

        Schema::table('legacy_results', function (Blueprint $table): void {
            $table->text('message')->after('status')->nullable();
        });

        Schema::table('legacy_final_results', function (Blueprint $table): void {
            $table->text('message')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legacy_students', function (Blueprint $table): void {
            $table->dropColumn('message');
        });

        Schema::table('legacy_results', function (Blueprint $table): void {
            $table->dropColumn('message');
        });

        Schema::table('legacy_final_results', function (Blueprint $table): void {
            $table->dropColumn('message');
        });
    }
};
