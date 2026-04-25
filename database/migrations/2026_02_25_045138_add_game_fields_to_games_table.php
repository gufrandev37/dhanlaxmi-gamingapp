<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('game_name');
            $table->time('result_time')->nullable()->after('status');
            $table->enum('play_next_day', ['yes', 'no'])->default('no')->after('close_time');
            $table->json('play_days')->nullable()->after('play_next_day');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'game_name',
                'result_time',
                'play_next_day',
                'play_days',
            ]);
        });
    }
};