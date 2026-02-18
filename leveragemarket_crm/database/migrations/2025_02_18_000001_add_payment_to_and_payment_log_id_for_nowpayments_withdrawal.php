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
        if (Schema::hasTable('payment_logs')) {
            Schema::table('payment_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_logs', 'payment_to')) {
                    $table->string('payment_to', 500)->nullable()->after('initiated_by');
                }
            });
        }

        if (Schema::hasTable('wallet_withdraw')) {
            Schema::table('wallet_withdraw', function (Blueprint $table) {
                if (!Schema::hasColumn('wallet_withdraw', 'payment_log_id')) {
                    $table->unsignedBigInteger('payment_log_id')->nullable()->after('wallet_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payment_logs') && Schema::hasColumn('payment_logs', 'payment_to')) {
            Schema::table('payment_logs', function (Blueprint $table) {
                $table->dropColumn('payment_to');
            });
        }

        if (Schema::hasTable('wallet_withdraw') && Schema::hasColumn('wallet_withdraw', 'payment_log_id')) {
            Schema::table('wallet_withdraw', function (Blueprint $table) {
                $table->dropColumn('payment_log_id');
            });
        }
    }
};
