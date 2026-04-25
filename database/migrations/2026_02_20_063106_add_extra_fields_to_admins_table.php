<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToAdminsTable extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('admin_id')->unique()->nullable()->after('id');
            $table->string('image')->nullable()->after('admin_id');
            $table->string('aadhaar_number')->nullable()->after('image');
            $table->string('pan_number')->nullable()->after('aadhaar_number');
            $table->string('driving_license')->nullable()->after('pan_number');
            $table->string('status')->default('pending')->after('driving_license');
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
                'admin_id',
                'image',
                'aadhaar_number',
                'pan_number',
                'driving_license',
                'status',
            ]);
        });
    }
}