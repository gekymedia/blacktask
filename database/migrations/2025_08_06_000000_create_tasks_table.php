<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateTasksTable Migration
 *
 * This migration defines the schema for the tasks table used by
 * BLACKTASK. Each record represents a single to‑do item for a given
 * date. Additional fields such as a reminder timestamp are included
 * for future extensions of the application.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->date('task_date')->default(now());
            $table->timestamp('reminder_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};