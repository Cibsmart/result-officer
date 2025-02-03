<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_curriculum_semesters', function (Blueprint $table) {
            $table->tinyInteger('maximum_credit_units')->default(24)->after('minimum_elective_units');
            $table->tinyInteger('minimum_credit_units')->default(15)->after('minimum_elective_units');
        });
    }

    public function down(): void
    {
        Schema::table('program_curriculum_semesters', function (Blueprint $table) {
            $table->dropColumn(['minimum_credit_units', 'maximum_credit_units']);
        });
    }
};
