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
        Schema::table('tasks', function (Blueprint $table) {
            // Add category_id if it doesn't exist
            if (!Schema::hasColumn('tasks', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }

            // Add priority if it doesn't exist
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->tinyInteger('priority')->default(1)->after('category_id')->comment('0=Low, 1=Medium, 2=High');
            }

            // Add indexes for better query performance
            $table->index('user_id');
            $table->index('task_date');
            $table->index(['user_id', 'task_date']);
            $table->index('category_id');
            $table->index('is_done');
            $table->index(['user_id', 'is_done', 'task_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['user_id', 'is_done', 'task_date']);
            $table->dropIndex(['user_id', 'task_date']);
            $table->dropIndex(['task_date']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_done']);

            // Drop columns if they exist
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('tasks', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};
