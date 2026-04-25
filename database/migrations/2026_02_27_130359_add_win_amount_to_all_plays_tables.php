<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // game_plays table
        Schema::table('game_plays', function (Blueprint $table) {
            $table->integer('win_amount')->default(0)->after('amount');

            
        });

        // andar_plays table
        Schema::table('andar_plays', function (Blueprint $table) {
            $table->integer('win_amount')->default(0)->after('amount');
        });

        // bahar_plays table
        Schema::table('bahar_plays', function (Blueprint $table) {
            $table->integer('win_amount')->default(0)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->dropColumn('win_amount');
        });

        Schema::table('andar_plays', function (Blueprint $table) {
            $table->dropColumn('win_amount');
        });

        Schema::table('bahar_plays', function (Blueprint $table) {
            $table->dropColumn('win_amount');
        });
    }
};