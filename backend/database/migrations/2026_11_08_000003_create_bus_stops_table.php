<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_stops', function (Blueprint $table) {
            $table->id();
            $table->string('name_hi');
            $table->string('name_en');
            $table->string('district');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            if (DB::getDriverName() === 'pgsql') {
                $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            }
            $table->boolean('is_major_junction')->default(false);
            $table->timestamps();

            $table->index('name_en');
            $table->index('name_hi');
            $table->index('district');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bus_stops_location ON bus_stops USING GIST (location);');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_stops');
    }
};

