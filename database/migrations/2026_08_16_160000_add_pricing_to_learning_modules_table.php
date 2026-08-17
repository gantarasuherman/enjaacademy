<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('is_featured');
            $table->unsignedInteger('price')->nullable()->after('is_paid');
        });
    }

    public function down(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'price']);
        });
    }
};
