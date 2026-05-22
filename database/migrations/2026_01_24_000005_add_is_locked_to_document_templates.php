<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLockedToDocumentTemplates extends Migration
{
    public function up()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_system');
        });
    }

    public function down()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
}
