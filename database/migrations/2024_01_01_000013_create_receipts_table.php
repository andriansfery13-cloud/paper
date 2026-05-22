<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->string('document_hash')->nullable();
            $table->string('verification_code')->unique()->nullable();
            $table->string('signature_image')->nullable();
            $table->string('stamp_image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'receipt_number']);
            $table->index(['tenant_id', 'invoice_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipts');
    }
}
