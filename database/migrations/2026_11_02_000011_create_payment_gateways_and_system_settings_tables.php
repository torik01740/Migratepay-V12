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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 50)->unique();
            $table->string('icon')->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->text('instructions')->nullable();
            $table->decimal('fee_percentage', 5, 2)->default(0.00);
            $table->decimal('fee_fixed', 15, 2)->default(0.00);
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->string('store_id')->nullable();
            $table->text('store_password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('sandbox_mode')->default(false);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->string('type', 50)->default('string');
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('payment_gateways');
    }
};
