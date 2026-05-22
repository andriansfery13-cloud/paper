<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaySettingsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('gateway'); // midtrans, xendit, stripe
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->text('server_key')->nullable();
            $table->text('client_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'gateway']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
}
