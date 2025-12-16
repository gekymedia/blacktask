<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->boolean('browser_notifications')->default(true)->after('remember_token');
            $table->boolean('email_notifications')->default(true)->after('browser_notifications');
            $table->boolean('whatsapp_notifications')->default(false)->after('email_notifications');
            $table->boolean('sms_notifications')->default(false)->after('whatsapp_notifications');
            $table->boolean('gekychat_notifications')->default(false)->after('sms_notifications');
            $table->time('notification_time')->default('09:00:00')->after('gekychat_notifications')->comment('Default time for daily reminders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'browser_notifications',
                'email_notifications',
                'whatsapp_notifications',
                'sms_notifications',
                'gekychat_notifications',
                'notification_time',
            ]);
        });
    }
};

