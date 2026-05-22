<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 15, 2)->default(0);
            $table->decimal('price_yearly', 15, 2)->default(0);
            $table->integer('max_invoices')->default(-1); // -1 = unlimited
            $table->integer('max_clients')->default(-1);
            $table->integer('max_users')->default(-1);
            $table->integer('max_products')->default(-1);
            $table->integer('max_quotations')->default(-1);
            $table->boolean('has_payment_gateway')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_custom_template')->default(false);
            $table->boolean('has_recurring_invoice')->default(false);
            $table->boolean('has_multi_currency')->default(false);
            $table->integer('included_tokens')->default(0);
            $table->integer('trial_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}
