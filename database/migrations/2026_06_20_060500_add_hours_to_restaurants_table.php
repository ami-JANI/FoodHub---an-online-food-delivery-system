<?php

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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->time('opening_time')->nullable()->after('is_open');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->boolean('is_manually_closed')->default(false)->after('closing_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['opening_time', 'closing_time', 'is_manually_closed']);
        });
    }
};
