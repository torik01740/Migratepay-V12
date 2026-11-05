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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'kyc_document_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('kyc_document_path', 255)->nullable()->after('id_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'kyc_document_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('kyc_document_path');
            });
        }
    }
};

