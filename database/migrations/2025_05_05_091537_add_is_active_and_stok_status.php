<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('image');
            $table->boolean('is_available')->default(true)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_available']);
        });
    }
};