<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank_transfer, credit_card, va, qris, ewallet
            $table->string('reference_number')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->text('gateway_response')->nullable();
            $table->decimal('gateway_fee', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded', 'expired'])->default('pending');
            $table->string('proof_of_payment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'payment_number']);
            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'status']);
            $table->index('gateway_transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
