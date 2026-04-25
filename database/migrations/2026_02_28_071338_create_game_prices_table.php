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
        Schema::create('game_prices', function (Blueprint $table) {
            $table->id();
            $table->string('game_type');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('multiply')->default(10);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_prices');
    }
};
