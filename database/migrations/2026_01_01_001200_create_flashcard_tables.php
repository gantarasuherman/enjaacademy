<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_decks', function (Blueprint $table) {
            $table->id();
            // NULL owner = system deck published by an admin.
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('learning_module_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 30)->default('indigo');
            $table->string('icon')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'is_public']);
        });

        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_deck_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_item_id')->nullable()->constrained()->nullOnDelete();
            $table->text('front');
            $table->text('back');
            $table->string('hint')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['flashcard_deck_id', 'sort_order']);
        });

        // SM-2 spaced repetition state, one row per user per card.
        Schema::create('flashcard_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->decimal('ease_factor', 4, 2)->default(2.50);
            $table->unsignedInteger('interval_days')->default(0);
            $table->unsignedInteger('repetitions')->default(0);
            $table->unsignedInteger('lapses')->default(0);
            $table->unsignedTinyInteger('last_quality')->nullable(); // 0..5
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'flashcard_id']);
            $table->index(['user_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_reviews');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_decks');
    }
};
