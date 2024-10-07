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
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json("content")->nullable();
            $table->json("tags")->nullable();
            $table->foreignId("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreignId("previous_id")->nullable();
            $table->foreign("previous_id")->references("id")->on("posts");
        });
        Schema::create('queued_posts', function (Blueprint $table) {
            $table->id();
            $table->boolean('processed')->default(false);
            $table->timestamp('scheduled_time');
            $table->timestamps();
            $table->json("content")->nullable();
            $table->json("tags")->nullable();
            $table->foreignId("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreignId("previous_id")->nullable();
            $table->foreign("previous_id")->references("id")->on("posts");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafts');
        Schema::dropIfExists('queued_posts');
    }
};
