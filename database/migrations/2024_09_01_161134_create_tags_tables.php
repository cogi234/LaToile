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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("name", 32);
        });
        
        Schema::create("post_has_tags", function (Blueprint $table) {
            $table->foreignId("post_id");
            $table->foreignId("tag_id");
            $table->timestamps();
            $table->boolean("indexed")->default(false);
            $table->primary(["post_id", "tag_id"]);
            $table->foreign("post_id")->references("id")->on("posts");
            $table->foreign("tag_id")->references("id")->on("tags");
        });

        Schema::create("followed_tags", function (Blueprint $table) {
            $table->foreignId("user_id");
            $table->foreignId("tag_id");
            $table->primary(["user_id", "tag_id"]);
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("tag_id")->references("id")->on("tags");
        });

        Schema::create("blocked_tags", function (Blueprint $table) {
            $table->foreignId("user_id");
            $table->foreignId("tag_id");
            $table->primary(["user_id", "tag_id"]);
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("tag_id")->references("id")->on("tags");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_has_tags');
        Schema::dropIfExists('followed_tags');
        Schema::dropIfExists('blocked_tags');
    }
};
