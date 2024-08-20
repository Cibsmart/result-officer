<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Support\PulseMigration;

return new class extends PulseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->shouldRun()) {
            return;
        }

        $this->createPulseValuesTable();

        $this->createPulseEntriesTable();

        $this->createPulseAggregationTable();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_values');
        Schema::dropIfExists('pulse_entries');
        Schema::dropIfExists('pulse_aggregates');
    }

    private function createPulseAggregationTable(): void
    {
        Schema::create('pulse_aggregates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('bucket');
            $table->unsignedMediumInteger('period');
            $table->string('type');
            $table->mediumText('key');
            match ($this->driver()) {
                'mariadb', 'mysql' => $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))'),
                'pgsql' => $table->uuid('key_hash')->storedAs('md5("key")::uuid'),
                'sqlite' => $table->string('key_hash'),
            };
            $table->string('aggregate');
            $table->decimal('value', 20, 2);
            $table->unsignedInteger('count')->nullable();

            // Force "on duplicate update"...
            $table->unique(['bucket', 'period', 'type', 'aggregate', 'key_hash']);
            // For trimming...
            $table->index(['period', 'bucket']);
            // For purging...
            $table->index('type');
            // For aggregate queries...
            $table->index(['period', 'type', 'aggregate', 'bucket']);
        });
    }

    private function createPulseEntriesTable(): void
    {
        Schema::create('pulse_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('timestamp');
            $table->string('type');
            $table->mediumText('key');
            match ($this->driver()) {
                'mariadb', 'mysql' => $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))'),
                'pgsql' => $table->uuid('key_hash')->storedAs('md5("key")::uuid'),
                'sqlite' => $table->string('key_hash'),
            };
            $table->bigInteger('value')->nullable();

            // For trimming...
            $table->index('timestamp');
            // For purging...
            $table->index('type');
            // For mapping...
            $table->index('key_hash');
            // For aggregate queries...
            $table->index(['timestamp', 'type', 'key_hash', 'value']);
        });
    }

    private function createPulseValuesTable(): void
    {
        Schema::create('pulse_values', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('timestamp');
            $table->string('type');
            $table->mediumText('key');
            match ($this->driver()) {
                'mariadb', 'mysql' => $table->char('key_hash', 16)->charset('binary')->virtualAs('unhex(md5(`key`))'),
                'pgsql' => $table->uuid('key_hash')->storedAs('md5("key")::uuid'),
                'sqlite' => $table->string('key_hash'),
            };
            $table->mediumText('value');

            // For trimming...
            $table->index('timestamp');
            // For fast lookups and purging...
            $table->index('type');
            // For data integrity and upserts...
            $table->unique(['type', 'key_hash']);
        });
    }
};
