<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code')->unique();
            $table->string('document_type'); // invoice, quotation, receipt, delivery_note
            $table->unsignedBigInteger('document_id');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('document_hash');
            $table->integer('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->string('last_viewed_ip')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'document_id']);
            $table->index('verification_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_verifications');
    }
}
