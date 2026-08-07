<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('level', 20)->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->unsignedInteger('time_limit_seconds')->nullable();
            $table->unsignedTinyInteger('pass_score')->default(70);
            $table->unsignedInteger('xp_reward')->default(50);
            $table->unsignedInteger('max_attempts')->nullable();
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('show_explanation')->default(true);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['learning_module_id', 'is_published']);
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank', 'matching'])
                ->default('multiple_choice');
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->text('explanation')->nullable();
            $table->string('correct_text')->nullable();  // fill_blank answer key
            $table->unsignedInteger('score')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->text('label');
            $table->string('image_path')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quiz_question_id', 'sort_order']);
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->unsignedInteger('score')->default(0);       // 0-100
            $table->unsignedInteger('earned_xp')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'quiz_id']);
            $table->index(['quiz_id', 'score']);
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_option_id')->nullable()->constrained()->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['quiz_attempt_id', 'quiz_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
