<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryNotesTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('delivery_number')->unique();
            $table->date('delivery_date');
            $table->string('recipient_name');
            $table->text('recipient_address')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->string('received_by_name')->nullable();
            $table->string('receiver_signature')->nullable();
            $table->string('document_hash')->nullable();
            $table->string('verification_code')->unique()->nullable();
            $table->string('signature_image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'delivery_number']);
            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('unit')->default('pcs');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
    }
}
