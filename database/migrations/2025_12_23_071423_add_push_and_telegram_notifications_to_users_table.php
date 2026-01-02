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
            $table->boolean('push_notifications')->default(false)->after('gekychat_notifications');
            $table->boolean('telegram_notifications')->default(false)->after('push_notifications');
            $table->string('telegram_chat_id')->nullable()->after('telegram_notifications');
            $table->string('push_token')->nullable()->after('telegram_chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['push_notifications', 'telegram_notifications', 'telegram_chat_id', 'push_token']);
        });
    }
};
