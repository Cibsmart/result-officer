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
        Schema::create('user_departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Department::class)->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_departments');
    }
};
