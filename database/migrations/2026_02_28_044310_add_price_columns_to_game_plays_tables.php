<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_plays_tables', function (Blueprint $table) {
            // game_plays
            Schema::table('game_plays', function (Blueprint $table) {
                $table->boolean('is_price_config')->default(false)->after('status');
                $table->decimal('price', 10, 2)->nullable()->after('is_price_config');
            });

            // andar_plays
            Schema::table('andar_plays', function (Blueprint $table) {
                $table->boolean('is_price_config')->default(false)->after('status');
                $table->decimal('price', 10, 2)->nullable()->after('is_price_config');
            });

            // bahar_plays
            Schema::table('bahar_plays', function (Blueprint $table) {
                $table->boolean('is_price_config')->default(false)->after('status');
                $table->decimal('price', 10, 2)->nullable()->after('is_price_config');
            });

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_plays_tables', function (Blueprint $table) {
            Schema::table('game_plays', function (Blueprint $table) {
                $table->dropColumn(['is_price_config', 'price']);
            });

            Schema::table('andar_plays', function (Blueprint $table) {
                $table->dropColumn(['is_price_config', 'price']);
            });

            Schema::table('bahar_plays', function (Blueprint $table) {
                $table->dropColumn(['is_price_config', 'price']);
            });

        });
    }
};
