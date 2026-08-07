<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The single source of truth for every navigation element in the app.
 * Nothing about the sidebar, topbar or footer is hardcoded in Blade.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();

            // Either a named route (preferred — survives URL changes) or a raw URL.
            $table->string('route_name')->nullable();
            $table->string('route_params')->nullable();
            $table->string('url')->nullable();

            $table->enum('type', ['menu', 'divider', 'header', 'external'])->default('menu');
            $table->enum('position', ['sidebar', 'topbar', 'footer'])->default('sidebar');

            $table->string('badge', 40)->nullable();
            $table->string('badge_color', 30)->nullable();

            // Visibility is permission driven; NULL means "everyone signed in".
            $table->string('permission_name')->nullable();
            $table->string('role_default')->nullable();

            $table->enum('target', ['_self', '_blank'])->default('_self');

            $table->boolean('is_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sidebar')->default(true);
            $table->boolean('is_topbar')->default(false);
            $table->boolean('is_footer')->default(false);

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('description')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
            $table->index(['position', 'is_active', 'is_visible']);
            $table->index('permission_name');
        });

        // Menu Access Matrix: role x menu. Empty pivot for a menu means the
        // permission_name column alone decides visibility.
        Schema::create('menu_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['menu_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menus');
    }
};
