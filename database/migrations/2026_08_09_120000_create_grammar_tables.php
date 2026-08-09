<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A first-class Grammar system: levels (N5..N1) -> categories (self-nesting,
 * so a "subcategory" is just a category with a parent) -> patterns -> their
 * examples/mistakes. Every layer is admin-managed data, not hardcoded
 * frontend structure — adding a new level, category, or pattern is a row,
 * never a deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grammar_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // N5, N4, ... / a free-text label admin can invent
            $table->string('slug')->unique();
            $table->string('color', 30)->default('indigo');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('grammar_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grammar_level_id')->constrained()->cascadeOnDelete();
            // Null parent = top-level category; a category with a parent is a subcategory.
            $table->foreignId('parent_id')->nullable()->constrained('grammar_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['grammar_level_id', 'parent_id', 'slug']);
            $table->index(['grammar_level_id', 'sort_order']);
            $table->index('parent_id');
        });

        Schema::create('grammar_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grammar_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');               // です/ます, ～ながら, ...
            $table->string('slug')->unique();
            $table->text('explanation');
            $table->text('formula')->nullable();    // e.g. "V(ます形) + ながら"
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['grammar_category_id', 'sort_order']);
            $table->index(['is_published']);
        });

        Schema::create('grammar_pattern_examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grammar_pattern_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['example', 'mistake'])->default('example');
            $table->text('sentence');               // correct sentence, or the *wrong* one for a mistake row
            $table->text('translation')->nullable();
            // For a mistake row: the corrected sentence + why it was wrong.
            $table->text('correction')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['grammar_pattern_id', 'type', 'sort_order'], 'grammar_pattern_examples_pattern_type_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grammar_pattern_examples');
        Schema::dropIfExists('grammar_patterns');
        Schema::dropIfExists('grammar_categories');
        Schema::dropIfExists('grammar_levels');
    }
};
