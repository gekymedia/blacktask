<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;

return new class extends Migration
{
    public function up()
    {
        // 1. First make user_id nullable temporarily
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // 2. Assign all existing tasks to the first user (or appropriate user)
        if ($user = \App\Models\User::first()) {
            Task::whereNull('user_id')->update(['user_id' => $user->id]);
        }

        // 3. Now make the column not nullable and add foreign key
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};