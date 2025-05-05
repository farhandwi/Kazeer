<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dateTime('promo_start_at')->nullable();
            $table->dateTime('promo_end_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn('promo_start_at');
            $table->dropColumn('promo_end_at');
        });
    }
};
