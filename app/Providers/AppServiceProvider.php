<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Menu;
use App\Models\User;
use App\Observers\AuditableObserver;
use App\Observers\MenuObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        if (app()->isProduction()) {
            URL::forceScheme('https');
            DB::prohibitDestructiveCommands(true);
        }

        Password::defaults(fn () => app()->isProduction()
            ? Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(8)->letters()->numbers());

        // Morph map keeps polymorphic columns readable and refactor-proof.
        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
            'menu' => \App\Models\Menu::class,
            'role' => \Spatie\Permission\Models\Role::class,
            'permission' => \Spatie\Permission\Models\Permission::class,
            'language' => \App\Models\Language::class,
            'module' => \App\Models\LearningModule::class,
            'lesson' => \App\Models\Lesson::class,
            'lesson_item' => \App\Models\LessonItem::class,
            'quiz' => \App\Models\Quiz::class,
            'quiz_attempt' => \App\Models\QuizAttempt::class,
            'flashcard' => \App\Models\Flashcard::class,
            'flashcard_deck' => \App\Models\FlashcardDeck::class,
            'achievement' => \App\Models\Achievement::class,
        ]);

        Menu::observe(MenuObserver::class);

        // Anything the audit log should follow automatically.
        foreach ([User::class, Role::class, Permission::class] as $auditable) {
            $auditable::observe(AuditableObserver::class);
        }
    }
}
