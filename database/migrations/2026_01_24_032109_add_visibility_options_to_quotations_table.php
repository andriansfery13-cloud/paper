<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityOptionsToQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('include_signature')->default(true);
            $table->boolean('include_stamp')->default(true);
            $table->boolean('include_qr')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'include_signature')) {
                $table->dropColumn('include_signature');
            }
            if (Schema::hasColumn('quotations', 'include_stamp')) {
                $table->dropColumn('include_stamp');
            }
            if (Schema::hasColumn('quotations', 'include_qr')) {
                $table->dropColumn('include_qr');
            }
        });
    }
}
