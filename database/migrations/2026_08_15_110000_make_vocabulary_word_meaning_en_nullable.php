<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `meaning_en` only makes sense for English-target words (it's "what
     * this English word means, in English"). Japanese vocabulary has no such
     * field in the source data — only an Indonesian meaning — so this can no
     * longer be a hard requirement at the schema level.
     */
    public function up(): void
    {
        Schema::table('vocabulary_words', function (Blueprint $table) {
            $table->text('meaning_en')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vocabulary_words', function (Blueprint $table) {
            $table->text('meaning_en')->nullable(false)->change();
        });
    }
};
