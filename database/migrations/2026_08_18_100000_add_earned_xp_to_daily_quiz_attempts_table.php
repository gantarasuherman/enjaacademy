<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_quiz_attempts', function (Blueprint $table) {
            $table->unsignedInteger('earned_xp')->default(0)->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('daily_quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('earned_xp');
        });
    }
};
