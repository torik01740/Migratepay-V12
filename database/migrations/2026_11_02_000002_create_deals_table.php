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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('counterparty_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('deal_id', 50)->unique();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->decimal('buyer_pays', 15, 2);
            $table->decimal('seller_gets', 15, 2);
            $table->decimal('charge_total', 15, 2)->default(0.00);
            $table->string('counterparty_email');
            $table->string('initiator_role', 50)->default('buyer');
            $table->string('status', 50)->default('awaiting_acceptance');
            $table->date('deadline')->nullable();
            $table->longText('workspace')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('admin_approved')->default(false);
            $table->boolean('buyer_accepted')->default(false);
            $table->boolean('custom_service')->default(false);
            $table->decimal('custom_service_charge', 15, 2)->default(0.00);
            $table->boolean('buyer_reviewed')->default(false);
            $table->boolean('seller_reviewed')->default(false);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->decimal('original_charge', 15, 2)->nullable();
            $table->decimal('coupon_discount', 15, 2)->nullable();
            $table->timestamps();

            $table->index('initiator_id');
            $table->index('counterparty_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
