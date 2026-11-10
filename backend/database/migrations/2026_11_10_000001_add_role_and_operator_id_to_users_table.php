<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->after('email'); // 'admin', 'operator'
            $table->foreignId('operator_id')->nullable()->after('role')->constrained('operators')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
            $table->dropColumn(['role', 'operator_id']);
        });
    }
};

