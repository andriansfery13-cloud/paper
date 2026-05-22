<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationColumnsToTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('email_otp')->nullable()->after('email_verified_at');
            $table->string('phone_otp')->nullable()->after('phone_verified_at');
            $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp');
            $table->timestamp('phone_otp_expires_at')->nullable()->after('phone_otp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'phone_verified_at',
                'email_otp',
                'phone_otp',
                'email_otp_expires_at',
                'phone_otp_expires_at',
            ]);
        });
    }
}
