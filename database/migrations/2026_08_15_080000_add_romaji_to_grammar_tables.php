<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Japanese Grammar/Structure content has no romaji, unlike Vocabulary
 * (`term`/`reading`/`romaji`) — a student who can't read kana/kanji yet has
 * no way to sound out a pattern title or example sentence. English-language
 * rows simply leave these columns null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grammar_patterns', function (Blueprint $table) {
            $table->string('title_romaji')->nullable()->after('title');
        });

        Schema::table('grammar_pattern_examples', function (Blueprint $table) {
            $table->text('romaji')->nullable()->after('sentence');
        });
    }

    public function down(): void
    {
        Schema::table('grammar_patterns', function (Blueprint $table) {
            $table->dropColumn('title_romaji');
        });

        Schema::table('grammar_pattern_examples', function (Blueprint $table) {
            $table->dropColumn('romaji');
        });
    }
};
