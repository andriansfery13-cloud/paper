<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokenTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('amount');
            $table->enum('type', ['credit', 'debit']);
            $table->string('description');
            $table->nullableMorphs('reference'); // For polymorphic relation
            $table->integer('balance_after');
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('token_transactions');
    }
}
