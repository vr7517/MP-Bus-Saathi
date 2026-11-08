<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('bus_stop_id')->constrained('bus_stops')->restrictOnDelete();
            $table->string('device_hash', 64);
            $table->enum('report_type', ['on_time', 'delayed', 'cancelled', 'fare_update']);
            $table->integer('delay_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->useCurrent();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['schedule_id', 'verified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_verifications');
    }
};

