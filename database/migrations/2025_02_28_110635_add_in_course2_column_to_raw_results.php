<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_results', function (Blueprint $table): void {
            $table->integer('in_course_2')->default(0)->after('in_course');
        });
    }

    public function down(): void
    {
        Schema::table('raw_results', function (Blueprint $table): void {
            $table->dropColumn('in_course_2');
        });
    }
};
