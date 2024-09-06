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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("content")->nullable();
            $table->text("previous_content")->nullable();
            $table->foreignId("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreignId("original_id")->nullable();
            $table->foreign("original_id")->references("id")->on("posts");
            $table->foreignId("previous_id")->nullable();
            $table->foreign("previous_id")->references("id")->on("posts");
        });

        Schema::create('likes', function (Blueprint $table){
            $table->foreignId("user_id");
            $table->foreignId("post_id");
            $table->timestamps();
            $table->primary(["user_id", "post_id"]);
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("post_id")->references("id")->on("posts");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('likes');
    }
};
