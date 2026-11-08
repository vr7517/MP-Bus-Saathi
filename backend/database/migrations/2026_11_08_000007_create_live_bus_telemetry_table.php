<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_bus_telemetry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->unique()->constrained('buses')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            if (DB::getDriverName() === 'pgsql') {
                $table->geometry('current_location', subtype: 'point', srid: 4326)->nullable();
            }
            $table->float('current_speed')->default(0.0);
            $table->float('heading')->default(0.0);
            $table->enum('gps_status', ['live', 'delayed', 'unavailable'])->default('unavailable');
            $table->timestamp('last_ping_at');
            $table->foreignId('active_schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->timestamps();

            $table->index('last_ping_at');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_telemetry_location ON live_bus_telemetry USING GIST (current_location);');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('live_bus_telemetry');
    }
};

