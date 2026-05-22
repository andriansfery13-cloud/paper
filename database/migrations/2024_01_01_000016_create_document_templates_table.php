<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); // null = system template
            $table->string('name');
            $table->enum('type', ['invoice', 'quotation', 'receipt', 'delivery_note']);
            $table->longText('html_content');
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_templates');
    }
}
