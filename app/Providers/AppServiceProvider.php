<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Achievement;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\GrammarPattern;
use App\Models\Language;
use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Menu;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Observers\AuditableObserver;
use App\Observers\MenuObserver;
use App\Services\AI\GeminiClient;
use App\Services\Setting\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
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
        // DB setting (admin "Pengaturan → Integrasi") wins when set; .env is
        // only the bootstrap/fallback path for before an admin configures it.
        $this->app->singleton(GeminiClient::class, function ($app) {
            $settings = $app->make(SettingService::class);

            return new GeminiClient(
                $settings->get('gemini_api_key') ?: config('services.gemini.key'),
                $settings->get('gemini_model') ?: config('services.gemini.model'),
            );
        });
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        // Every admin listing's ->links() renders through this instead of
        // Laravel's stock gray/blue Tailwind view, which clashed with the
        // panel's slate + brand color system and had no real dark mode.
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        if (app()->isProduction()) {
            URL::forceScheme('https');
            DB::prohibitDestructiveCommands(true);
        }

        Password::defaults(fn () => app()->isProduction()
            ? Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(8)->letters()->numbers());

        // Morph map keeps polymorphic columns readable and refactor-proof.
        Relation::enforceMorphMap([
            'user' => User::class,
            'menu' => Menu::class,
            'role' => Role::class,
            'permission' => Permission::class,
            'language' => Language::class,
            'module' => LearningModule::class,
            'lesson' => Lesson::class,
            'lesson_item' => LessonItem::class,
            'quiz' => Quiz::class,
            'quiz_attempt' => QuizAttempt::class,
            'grammar_pattern' => GrammarPattern::class,
            'flashcard' => Flashcard::class,
            'flashcard_deck' => FlashcardDeck::class,
            'achievement' => Achievement::class,
        ]);

        Menu::observe(MenuObserver::class);

        // Anything the audit log should follow automatically.
        foreach ([User::class, Role::class, Permission::class] as $auditable) {
            $auditable::observe(AuditableObserver::class);
        }
    }
}
