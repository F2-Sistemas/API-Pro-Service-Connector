<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_country')->nullable()->index();
            $table->string('phone_number')->nullable()->index();
            $table->timestamp('phone_sms_verified_at')->nullable()->index();
            $table->timestamp('phone_whatsapp_verified_at')->nullable()->index();
            $table->timestamp('phone_telegram_verified_at')->nullable()->index();

            $table->unique([
                'phone_country',
                'phone_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_country']);
            $table->dropIndex(['phone_number']);
            $table->dropIndex(['phone_sms_verified_at']);
            $table->dropIndex(['phone_whatsapp_verified_at']);
            $table->dropIndex(['phone_telegram_verified_at']);

            $table->dropUnique([
                'phone_country',
                'phone_number',
            ]);

            $table->dropColumn(['phone_country']);
            $table->dropColumn(['phone_number']);
            $table->dropColumn(['phone_sms_verified_at']);
            $table->dropColumn(['phone_whatsapp_verified_at']);
            $table->dropColumn(['phone_telegram_verified_at']);
        });
    }
};
