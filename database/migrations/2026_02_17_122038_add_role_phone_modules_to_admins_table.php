<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {

            if (!Schema::hasColumn('admins', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }

            if (!Schema::hasColumn('admins', 'role')) {
                $table->string('role')->default('admin')->after('phone');
            }

            if (!Schema::hasColumn('admins', 'modules')) {
                $table->json('modules')->nullable()->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'modules']);
        });
    }
};
