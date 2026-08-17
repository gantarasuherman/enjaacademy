<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A checkout/payment record for a paid course. `reference` (e.g. "ORD-000123")
 * is deliberately NOT a column — `Order::reference` derives it from `id`, so
 * there's no uniqueness/collision concern to manage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_module_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('payment_method')->nullable(); // 'simulated' today; a real gateway name once one is wired up
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'learning_module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
