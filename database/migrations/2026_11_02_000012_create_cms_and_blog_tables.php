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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('status', 50)->default('published');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 150)->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('category', 100)->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status', 50)->default('published');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('cms_pages');
    }
};
