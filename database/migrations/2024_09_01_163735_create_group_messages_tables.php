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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create("group_memberships", function (Blueprint $table) {
            $table->timestamps();
            $table->enum("status", ["invite", "member"]);
            $table->foreignId("user");
            $table->foreignId("group");
            $table->primary(["user", "group"]);
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("group")->references("id")->on("groups");
        });
        
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("message");
            $table->boolean("read")->default(false);
            $table->foreignId("user");
            $table->foreignId("group");
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("group")->references("id")->on("groups");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_memberships');
        Schema::dropIfExists('group_messages');
    }
};
