<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Indonesia');
            $table->string('npwp')->nullable();
            $table->string('logo')->nullable();
            $table->string('stamp_image')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('website')->nullable();
            $table->json('settings')->nullable();
            $table->json('invoice_settings')->nullable();
            $table->string('invoice_prefix')->default('INV');
            $table->string('quotation_prefix')->default('QUO');
            $table->string('receipt_prefix')->default('REC');
            $table->string('delivery_prefix')->default('DO');
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->foreignId('current_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->integer('token_balance')->default(0);
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('currency')->default('IDR');
            $table->string('date_format')->default('d/m/Y');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
