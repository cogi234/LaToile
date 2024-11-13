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
            $table->text("avatar")->nullable();
            $table->text("bio")->nullable();
            $table->boolean("moderator")->default(false);
            $table->text(column: "profile_background")->nullable();
        });

        Schema::create("followed_users", function (Blueprint $table) {
            $table->foreignId("user");
            $table->foreignId("followed");
            $table->primary(["user", "followed"]);
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("followed")->references("id")->on("users");
        });

        Schema::create("blocked_users", function (Blueprint $table) {
            $table->foreignId("user");
            $table->foreignId("blocked");
            $table->primary(["user", "blocked"]);
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("blocked")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(["avatar", "bio", "moderator"]);
        });
        Schema::dropIfExists('followed_users');
        Schema::dropIfExists('blocked_users');
    }
};
