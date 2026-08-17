<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The `correction` field (the fixed sentence on a mistake row) needs romaji
 * too — it's the sentence a student should actually walk away remembering.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grammar_pattern_examples', function (Blueprint $table) {
            $table->text('correction_romaji')->nullable()->after('correction');
        });
    }

    public function down(): void
    {
        Schema::table('grammar_pattern_examples', function (Blueprint $table) {
            $table->dropColumn('correction_romaji');
        });
    }
};
