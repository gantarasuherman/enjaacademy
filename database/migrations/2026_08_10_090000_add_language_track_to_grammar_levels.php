<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Grammar CMS was originally Japanese-JLPT-only (implicit language,
 * implicit "grammar" track). It now needs to host English grammar plus a
 * separate "sentence structure" track for both languages, so a level's
 * identity becomes (language, track, slug) instead of a bare global slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grammar_levels', function (Blueprint $table) {
            $table->string('language', 20)->default('japanese')->after('name');
            $table->string('track', 20)->default('grammar')->after('language');
        });

        Schema::table('grammar_levels', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->unique(['language', 'track', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('grammar_levels', function (Blueprint $table) {
            $table->dropUnique(['language', 'track', 'slug']);
            $table->unique('slug');
            $table->dropColumn(['language', 'track']);
        });
    }
};
