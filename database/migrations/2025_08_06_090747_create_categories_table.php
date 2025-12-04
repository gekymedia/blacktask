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
       // migration file
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('color')->default('#3b82f6');
    $table->foreignId('user_id')->constrained();
    $table->timestamps();
});

Schema::table('tasks', function (Blueprint $table) {
    $table->foreignId('category_id')->nullable()->constrained();
    $table->tinyInteger('priority')->default(2); // 0=low, 1=medium, 2=high
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
