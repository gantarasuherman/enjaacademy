<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "Susun Kata" — Duolingo-style sentence building: student taps/arranges a
 * shuffled word bank into the correct order. Reuses `quiz_options` for the
 * word bank (each option = one word, `sort_order` = its correct position)
 * and `correct_text` for the space-joined answer key, so grading reuses the
 * exact same string-compare path as `fill_blank` — see
 * `QuizQuestion::isAnswerCorrect()`.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quiz_questions MODIFY type ENUM(
            'multiple_choice', 'true_false', 'fill_blank', 'matching', 'arrange'
        ) DEFAULT 'multiple_choice'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quiz_questions MODIFY type ENUM(
            'multiple_choice', 'true_false', 'fill_blank', 'matching'
        ) DEFAULT 'multiple_choice'");
    }
};
