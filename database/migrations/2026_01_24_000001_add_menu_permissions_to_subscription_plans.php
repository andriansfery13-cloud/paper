<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->json('menu_permissions')->nullable()->after('has_multi_currency');
        });
    }

    public function down()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('menu_permissions');
        });
    }
};
