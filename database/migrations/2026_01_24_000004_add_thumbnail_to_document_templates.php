<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbnailToDocumentTemplates extends Migration
{
    public function up()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
}
