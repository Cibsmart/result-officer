<?php

declare(strict_types=1);

use App\Enums\EntryMode;
use App\Models\Curriculum;
use App\Models\Program;
use App\Models\Session;
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
        Schema::create('program_curricula', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Program::class)->constrained();
            $table->foreignIdFor(Curriculum::class)->constrained();
            $table->foreignIdFor(Session::class, 'entry_session_id')->constrained();
            $table->string('entry_mode')->default(EntryMode::UTME->value);
            $table->timestamps();

            $table->unique(['program_id', 'entry_session_id', 'entry_mode'], 'program_curricula_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_curricula');
    }
};
