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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id_string', 50)->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->string('division', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->decimal('wallet_balance', 15, 2)->default(0.00);
            $table->string('status', 50)->default('Active');
            $table->string('role', 50)->default('user');
            $table->rememberToken();
            $table->string('username', 100)->nullable()->unique();
            $table->string('headline')->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->text('bio')->nullable();
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->text('education')->nullable();
            $table->string('certifications')->nullable();
            $table->text('professional_links')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('cover_position_y', 50)->nullable();
            $table->string('profile_position_x', 50)->nullable();
            $table->string('profile_position_y', 50)->nullable();
            $table->string('cover_position_x', 50)->nullable();
            $table->string('verification_status', 50)->default('NOT_VERIFIED');
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('verification_updated_at')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('id_type', 50)->nullable()->default('National ID (NID)');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
