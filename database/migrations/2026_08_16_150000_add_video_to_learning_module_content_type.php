<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE learning_modules MODIFY content_type ENUM(
            'kana', 'kanji', 'vocabulary', 'grammar', 'conversation',
            'listening', 'speaking', 'reading', 'writing', 'exam', 'video'
        ) DEFAULT 'vocabulary'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE learning_modules MODIFY content_type ENUM(
            'kana', 'kanji', 'vocabulary', 'grammar', 'conversation',
            'listening', 'speaking', 'reading', 'writing', 'exam'
        ) DEFAULT 'vocabulary'");
    }
};
