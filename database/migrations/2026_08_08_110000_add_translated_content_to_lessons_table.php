<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Bahasa Indonesia translation of `content`, shown behind a
            // "Terjemahkan" toggle on Reading — optional, most lessons
            // (kana/kanji drills, vocab lists) have no long-form content to
            // translate in the first place.
            $table->longText('translated_content')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('translated_content');
        });
    }
};
