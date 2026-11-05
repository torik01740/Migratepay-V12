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
        if (!Schema::hasTable('payment_webhook_idempotencies')) {
            Schema::create('payment_webhook_idempotencies', function (Blueprint $table) {
                $table->id();
                $table->string('gateway_slug', 50);
                $table->string('payment_reference', 100);
                $table->string('status', 20)->default('processing'); // processing, completed, failed
                $table->unsignedBigInteger('transaction_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->decimal('amount', 15, 2)->nullable();
                $table->text('response_payload')->nullable();
                $table->timestamps();

                // Database-level uniqueness guarantee for atomic idempotency under concurrency
                $table->unique(['gateway_slug', 'payment_reference'], 'uniq_gateway_payment_ref');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_idempotencies');
    }
};

