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
            $table->boolean("can_get_messages_from_anyone")->default(true);
        });
        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->foreignId('sender_id');
            $table->foreign("sender_id")->references("id")->on("users");
            $table->foreignId('receiver_id');
            $table->foreign("receiver_id")->references("id")->on("users");
            $table->timestamps();
        });
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('group_memberships', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreignId('group_id');
            $table->foreign("group_id")->references("id")->on("groups");
            $table->enum('status', ['invite', 'member', 'creator']);
            $table->timestamps();
            $table->primary(['user_id', 'group_id']);
        });
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->foreignId('user_id');
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreignId('group_id');
            $table->foreign("group_id")->references("id")->on("groups");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(["can_get_messages_from_anyone"]);
        });
        Schema::dropIfExists('private_messages');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_memberships');
        Schema::dropIfExists('group_messages');
    }
};
