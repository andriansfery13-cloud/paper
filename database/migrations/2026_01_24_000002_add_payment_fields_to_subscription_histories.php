<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPaymentFieldsToSubscriptionHistories extends Migration
{
    public function up()
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->string('order_id')->nullable()->after('plan_id');
            $table->string('billing_period')->nullable()->after('order_id');
            $table->timestamp('paid_at')->nullable()->after('ended_at');
        });

        // Modify status enum to include payment statuses
        DB::statement("ALTER TABLE subscription_histories MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'active', 'expired', 'cancelled', 'upgraded', 'challenge') DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'billing_period', 'paid_at']);
        });

        DB::statement("ALTER TABLE subscription_histories MODIFY COLUMN status ENUM('active', 'expired', 'cancelled', 'upgraded') DEFAULT 'active'");
    }
}
