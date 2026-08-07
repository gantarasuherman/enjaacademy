<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 60)->default('general');   // general | meta | mail | ...
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type', 20)->default('string');     // string|bool|int|json|file
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
