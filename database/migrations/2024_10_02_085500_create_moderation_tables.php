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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->text('reason');
            $table->boolean('handled')->default(false);
            $table->foreignId('post_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->foreignId('user_id');
            $table->foreignId('report_id')->nullable();
            $table->timestamps();
        });
        Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->text('reason');
            $table->timestamp('end_time')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('report_id')->nullable();
            $table->timestamps();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean("hidden")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(["hidden"]);
        });
        Schema::dropIfExists('reports');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('bans');
    }
};
