<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The single course a student is "currently taking" — drives which
     * language the Daily Quiz draws from. Nullable: a brand-new student has
     * none yet, and `DailyQuizService` falls back to English rather than
     * requiring one.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('active_module_id')->nullable()->after('id')
                ->constrained('learning_modules')->nullOnDelete();
        });

        // Backfill: users who already have enrollments but no active course
        // yet get their earliest enrollment as the default.
        DB::statement(<<<'SQL'
            UPDATE users
            SET active_module_id = (
                SELECT learning_module_id
                FROM enrollments
                WHERE enrollments.user_id = users.id
                ORDER BY enrolled_at ASC, id ASC
                LIMIT 1
            )
            WHERE active_module_id IS NULL
              AND EXISTS (SELECT 1 FROM enrollments WHERE enrollments.user_id = users.id)
        SQL);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('active_module_id');
        });
    }
};
