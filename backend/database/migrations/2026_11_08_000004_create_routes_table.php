<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_code')->unique();
            $table->foreignId('source_stop_id')->constrained('bus_stops')->restrictOnDelete();
            $table->foreignId('destination_stop_id')->constrained('bus_stops')->restrictOnDelete();
            $table->decimal('total_distance_km', 6, 2);
            $table->integer('estimated_duration_minutes');
            if (DB::getDriverName() === 'pgsql') {
                $table->geometry('route_path', subtype: 'linestring', srid: 4326)->nullable();
            }
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['source_stop_id', 'destination_stop_id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_routes_path ON routes USING GIST (route_path);');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};

