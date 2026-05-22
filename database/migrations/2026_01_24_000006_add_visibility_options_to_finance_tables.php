<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityOptionsToFinanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('include_signature')->default(true);
            $table->boolean('include_stamp')->default(true);
            $table->boolean('include_qr')->default(true);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->boolean('include_signature')->default(true);
            $table->boolean('include_stamp')->default(true);
            $table->boolean('include_qr')->default(true);
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
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
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'include_signature'))
                $table->dropColumn('include_signature');
            if (Schema::hasColumn('invoices', 'include_stamp'))
                $table->dropColumn('include_stamp');
            if (Schema::hasColumn('invoices', 'include_qr'))
                $table->dropColumn('include_qr');
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'include_signature'))
                $table->dropColumn('include_signature');
            if (Schema::hasColumn('receipts', 'include_stamp'))
                $table->dropColumn('include_stamp');
            if (Schema::hasColumn('receipts', 'include_qr'))
                $table->dropColumn('include_qr');
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'include_signature'))
                $table->dropColumn('include_signature');
            if (Schema::hasColumn('delivery_notes', 'include_stamp'))
                $table->dropColumn('include_stamp');
            if (Schema::hasColumn('delivery_notes', 'include_qr'))
                $table->dropColumn('include_qr');
        });
    }
}
