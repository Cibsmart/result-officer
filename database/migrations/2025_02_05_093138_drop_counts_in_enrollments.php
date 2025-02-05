<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['course_count', 'cus', 'gps', 'cgpas', 'fcgpa']);
        });

        Schema::table('session_enrollments', function (Blueprint $table) {
            $table->dropColumn(['course_count', 'cus', 'gps', 'gpas', 'cgpa']);
        });

        Schema::table('semester_enrollments', function (Blueprint $table) {
            $table->dropColumn(['course_count', 'cus', 'gps', 'gpa']);
        });
    }
};
