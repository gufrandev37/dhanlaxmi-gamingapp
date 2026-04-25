<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('wallets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('cin')->nullable();
        $table->decimal('amount', 12, 2);
        $table->enum('type', ['credit', 'debit'])->default('credit');
        $table->timestamps();

        // Performance indexes
        $table->index('cin');
        $table->index('created_at');
        $table->index('user_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
