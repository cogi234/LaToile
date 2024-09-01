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
            $table->boolean("indexed")->default(false);
        });
        
        Schema::create("post_has_tags", function (Blueprint $table) {
            $table->foreignId("post");
            $table->foreignId("tag");
            $table->timestamps();
            $table->primary(["post", "tag"]);
            $table->foreign("post")->references("id")->on("posts");
            $table->foreign("tag")->references("id")->on("tags");
        });

        Schema::create("followed_tags", function (Blueprint $table) {
            $table->foreignId("user");
            $table->foreignId("tag");
            $table->primary(["user", "tag"]);
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("tag")->references("id")->on("tags");
        });

        Schema::create("blocked_tags", function (Blueprint $table) {
            $table->foreignId("user");
            $table->foreignId("tag");
            $table->primary(["user", "tag"]);
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("tag")->references("id")->on("tags");
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
