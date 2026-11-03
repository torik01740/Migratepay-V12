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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->string('category', 100)->nullable();
            $table->text('keywords')->nullable();
            $table->string('status', 50)->default('Active');
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });

        Schema::create('ai_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->index();
            $table->string('sender_type', 50);
            $table->text('message');
            $table->string('visitor_name')->nullable();
            $table->string('visitor_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_histories');
        Schema::dropIfExists('ai_knowledge_bases');
        Schema::dropIfExists('ai_settings');
    }
};
