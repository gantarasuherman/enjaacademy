<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Splits short practice quizzes from long timed comprehensive tests (e.g. a
 * 100-question placement-style exam spanning every level) without adding a
 * parallel table — both reuse the exact same questions/attempts/timer
 * infrastructure, they just get a different landing page in the app.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('category', 20)->default('quiz')->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
