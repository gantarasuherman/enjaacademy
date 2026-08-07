<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('bookmarkable');
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id'], 'bookmarks_unique');
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('notable');
            $table->string('title')->nullable();
            $table->longText('body');
            $table->string('color', 30)->default('yellow');
            $table->boolean('is_pinned')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'is_pinned']);
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge_color', 30)->default('amber');

            // Evaluated by AchievementService against UserStat counters.
            $table->enum('criteria_type', [
                'xp_total', 'level', 'lessons_completed', 'quizzes_completed',
                'perfect_quizzes', 'streak_days', 'flashcards_reviewed', 'manual',
            ])->default('xp_total');
            $table->unsignedInteger('criteria_value')->default(0);
            $table->unsignedInteger('xp_reward')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('achievement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('progress')->default(0);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
        });

        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('trackable');                      // module | lesson
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('in_progress');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'trackable_type', 'trackable_id'], 'user_progress_unique');
            $table->index(['user_id', 'status']);
        });

        Schema::create('xp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->integer('amount');
            $table->string('reason', 120);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Denormalised counters so the dashboard never scans xp_logs.
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('xp_total')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('streak_days')->default(0);
            $table->unsignedInteger('longest_streak')->default(0);
            $table->date('last_active_date')->nullable();
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->unsignedInteger('quizzes_completed')->default(0);
            $table->unsignedInteger('perfect_quizzes')->default(0);
            $table->unsignedInteger('flashcards_reviewed')->default(0);
            $table->unsignedInteger('minutes_learned')->default(0);
            $table->timestamps();

            $table->index('xp_total');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stats');
        Schema::dropIfExists('xp_logs');
        Schema::dropIfExists('user_progress');
        Schema::dropIfExists('achievement_user');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('bookmarks');
    }
};
