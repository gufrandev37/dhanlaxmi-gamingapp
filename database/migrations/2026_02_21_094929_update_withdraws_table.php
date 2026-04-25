<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdraws', function (Blueprint $table) {

            if (Schema::hasColumn('withdraws', 'available_points')) {
                $table->dropColumn('available_points');
            }

            $table->enum('status', ['processing', 'approved', 'rejected'])
                  ->default('processing')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('withdraws', function (Blueprint $table) {

            // 🔄 rollback available_points
            $table->decimal('available_points', 10, 2)->default(0);

            // 🔄 rollback status
            $table->string('status')->default('processing')->change();
        });
    }
};