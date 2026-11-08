<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('bus_stop_id')->constrained('bus_stops')->restrictOnDelete();
            $table->integer('stop_sequence');
            $table->decimal('distance_from_source_km', 6, 2)->default(0.00);
            $table->integer('typical_travel_time_minutes')->default(0);
            $table->timestamps();

            $table->unique(['route_id', 'stop_sequence']);
            $table->index(['bus_stop_id', 'route_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_stops');
    }
};

