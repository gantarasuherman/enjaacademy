<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_method');
            $table->string('gateway_reference')->nullable()->unique()->after('gateway');
            $table->string('checkout_url')->nullable()->after('gateway_reference');
            $table->string('qr_url')->nullable()->after('checkout_url');
            $table->timestamp('expired_at')->nullable()->after('qr_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_reference', 'checkout_url', 'qr_url', 'expired_at']);
        });
    }
};
