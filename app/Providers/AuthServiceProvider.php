<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Achievement;
use App\Models\Enrollment;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use App\Models\GrammarPattern;
use App\Models\Language;
use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Quiz;
use App\Models\User;
use App\Models\VocabularyWord;
use App\Policies\AchievementPolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\FlashcardDeckPolicy;
use App\Policies\FlashcardPolicy;
use App\Policies\GrammarCategoryPolicy;
use App\Policies\GrammarLevelPolicy;
use App\Policies\GrammarPatternPolicy;
use App\Policies\LanguagePolicy;
use App\Policies\LearningModulePolicy;
use App\Policies\LessonItemPolicy;
use App\Policies\LessonPolicy;
use App\Policies\MenuPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\QuizPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\VocabularyWordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Menu::class => MenuPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Language::class => LanguagePolicy::class,
        LearningModule::class => LearningModulePolicy::class,
        Lesson::class => LessonPolicy::class,
        LessonItem::class => LessonItemPolicy::class,
        Quiz::class => QuizPolicy::class,
        FlashcardDeck::class => FlashcardDeckPolicy::class,
        Flashcard::class => FlashcardPolicy::class,
        Achievement::class => AchievementPolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Order::class => OrderPolicy::class,
        GrammarLevel::class => GrammarLevelPolicy::class,
        GrammarCategory::class => GrammarCategoryPolicy::class,
        GrammarPattern::class => GrammarPatternPolicy::class,
        VocabularyWord::class => VocabularyWordPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // The one and only hardcoded role reference in the codebase: the super
        // role short-circuits every gate, policy and `permission:` middleware.
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}
