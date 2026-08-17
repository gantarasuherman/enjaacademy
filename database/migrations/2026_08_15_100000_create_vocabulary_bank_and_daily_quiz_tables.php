<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A dedicated, dictionary-scale vocabulary bank (target: tens of thousands of
 * words) plus the Daily Quiz system built on top of it. This is intentionally
 * separate from `lessons`/`lesson_items` (which backs the existing
 * category-browsable Vocabulary page) — that system was designed for small,
 * curated per-lesson word lists, not a large, randomly-sampled quiz pool.
 * Both can coexist; this migration does not touch the existing tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('word');
            $table->string('phonetic')->nullable();          // IPA, e.g. /ˈæp.əl/
            $table->string('part_of_speech', 40)->nullable(); // noun, verb, adjective, ...
            $table->text('meaning_id');                       // Indonesian meaning
            $table->text('meaning_en');                       // English meaning/definition
            $table->string('level', 20);                      // Beginner..Advanced (English) / N5..N1 (Japanese, later)
            $table->json('synonyms')->nullable();
            $table->json('antonyms')->nullable();
            $table->json('collocations')->nullable();
            $table->timestamps();

            // The Daily Quiz's random-sampling query filters by (language, level)
            // and needs to page through ids fast at 20k+ rows — this composite
            // index is what keeps that query index-only instead of a table scan.
            $table->index(['language_id', 'level', 'id'], 'vocabulary_words_lang_level_id_idx');
            $table->index('word');
        });

        Schema::create('vocabulary_word_examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vocabulary_word_id')->constrained()->cascadeOnDelete();
            $table->text('sentence_en');
            $table->text('sentence_id')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['vocabulary_word_id', 'sort_order']);
        });

        Schema::create('daily_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('quiz_date'); // the calendar day this attempt is *for*, not necessarily created_at's date
            $table->unsignedTinyInteger('total_questions')->default(0);
            $table->unsignedTinyInteger('correct_count')->default(0);
            $table->unsignedTinyInteger('score')->default(0); // 0-100
            $table->boolean('skipped')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // A user gets exactly one attempt row per calendar day — this is
            // what "once per day" and the skip button's "max 1x/day" both rely on.
            $table->unique(['user_id', 'quiz_date']);
        });

        Schema::create('daily_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_word_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'fill_blank', 'matching', 'true_false', 'context'])
                ->default('multiple_choice');
            $table->json('payload'); // question-type-specific shape: options/prompt/correct answer key
            $table->string('given_answer')->nullable();
            $table->boolean('is_correct')->nullable(); // null until answered
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['daily_quiz_attempt_id', 'sort_order']);
        });

        Schema::create('user_weak_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_word_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('wrong_count')->default(1);
            $table->unsignedInteger('correct_streak')->default(0); // consecutive correct answers since last miss
            $table->timestamp('last_wrong_at')->nullable();
            $table->boolean('mastered')->default(false); // auto-set once correct_streak reaches the mastery threshold
            $table->timestamps();

            $table->unique(['user_id', 'vocabulary_word_id']);
            $table->index(['user_id', 'mastered']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_weak_words');
        Schema::dropIfExists('daily_quiz_questions');
        Schema::dropIfExists('daily_quiz_attempts');
        Schema::dropIfExists('vocabulary_word_examples');
        Schema::dropIfExists('vocabulary_words');
    }
};
