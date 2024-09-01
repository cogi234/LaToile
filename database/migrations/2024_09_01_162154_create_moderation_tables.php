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
            $table->timestamps();
            $table->text("reason");
            $table->boolean("handled")->default(false);
            $table->foreignId("user");
            $table->foreignId("post");
            $table->foreign("user")->references("id")->on("users");
            $table->foreign("post")->references("id")->on("posts");
        });

        Schema::create("warnings", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("message");
            $table->foreignId("report")->nullable();
            $table->foreign("report")->references("id")->on("reports");
            $table->foreignId("user");
            $table->foreign("user")->references("id")->on("users");
        });

        Schema::create("bans", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime("endTime")->nullable();
            $table->text("reason");
            $table->foreignId("report")->nullable();
            $table->foreign("report")->references("id")->on("reports");
            $table->foreignId("user");
            $table->foreign("user")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('bans');
    }
};
