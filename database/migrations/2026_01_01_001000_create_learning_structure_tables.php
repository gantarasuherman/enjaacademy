<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Languages -> learning modules -> lessons -> lesson items.
 *
 * A "module" (Hiragana, Kanji, TOEFL, ...) is a *row*, not a class. Adding a
 * new module is data entry in the admin panel plus a menu row pointing at the
 * generic learning routes — no deployment required.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Bahasa Jepang / Bahasa Inggris
            $table->string('slug', 60)->unique();   // japanese / english
            $table->string('code', 10);             // ja / en
            $table->string('flag', 10)->nullable(); // emoji flag
            $table->string('icon')->nullable();
            $table->string('color', 30)->default('indigo');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name');                 // Hiragana, Kanji, TOEFL ...
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color', 30)->default('indigo');

            // Drives which player/editor UI the generic controllers render.
            $table->enum('content_type', [
                'kana', 'kanji', 'vocabulary', 'grammar', 'conversation',
                'listening', 'speaking', 'reading', 'writing', 'exam',
            ])->default('vocabulary');

            // `{permission_prefix}.view|create|update|delete` gate this module.
            $table->string('permission_prefix')->nullable();

            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['language_id', 'sort_order']);
            $table->index(['is_active', 'content_type']);
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('level', 20)->nullable();   // N5..N1 / A1..C2 / TOEFL band
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();   // rich text / markdown
            $table->string('cover_image')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('estimated_minutes')->default(10);
            $table->unsignedInteger('xp_reward')->default(20);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['learning_module_id', 'sort_order']);
            $table->index(['is_published', 'level']);
        });

        // One generic item table serves every content type. `extra` carries the
        // type-specific bits (stroke count, JLPT tag, speaker name, ...).
        Schema::create('lesson_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('term');                       // あ / 日 / apple / grammar point
            $table->string('reading')->nullable();        // furigana / phonetics
            $table->string('romaji')->nullable();
            $table->string('meaning')->nullable();
            $table->text('example')->nullable();
            $table->text('example_meaning')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->json('extra')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lesson_id', 'sort_order']);
            $table->index('term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_items');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('learning_modules');
        Schema::dropIfExists('languages');
    }
};
