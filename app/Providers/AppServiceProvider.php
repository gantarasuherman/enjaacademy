<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Achievement;
use App\Models\DailyQuizAttempt;
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
use App\Services\AI\AiClientInterface;
use App\Services\AI\GeminiClient;
use App\Services\AI\GrokClient;
use App\Services\AI\GroqClient;
use App\Services\Payment\TripayGateway;
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

        $this->app->singleton(GrokClient::class, function ($app) {
            $settings = $app->make(SettingService::class);

            return new GrokClient(
                $settings->get('grok_api_key') ?: config('services.grok.key'),
                $settings->get('grok_model') ?: config('services.grok.model'),
            );
        });

        $this->app->singleton(GroqClient::class, function ($app) {
            $settings = $app->make(SettingService::class);

            return new GroqClient(
                $settings->get('groq_api_key') ?: config('services.groq.key'),
                $settings->get('groq_model') ?: config('services.groq.model'),
            );
        });

        // Which provider backs every "Buat dengan AI" feature — admin-selectable
        // (Pengaturan → Integrasi → "Provider AI Aktif"), defaults to Gemini.
        $this->app->singleton(AiClientInterface::class, function ($app) {
            $settings = $app->make(SettingService::class);

            return match ($settings->get('ai_provider', 'gemini')) {
                'grok' => $app->make(GrokClient::class),
                'groq' => $app->make(GroqClient::class),
                default => $app->make(GeminiClient::class),
            };
        });

        $this->app->singleton(TripayGateway::class, function ($app) {
            $settings = $app->make(SettingService::class);

            return new TripayGateway(
                $settings->get('tripay_merchant_code') ?: config('services.tripay.merchant_code'),
                $settings->get('tripay_api_key') ?: config('services.tripay.api_key'),
                $settings->get('tripay_private_key') ?: config('services.tripay.private_key'),
                config('services.tripay.sandbox', true),
                config('services.tripay.method', 'QRIS2'),
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
            'daily_quiz_attempt' => DailyQuizAttempt::class,
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
