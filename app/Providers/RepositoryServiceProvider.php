<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts;
use App\Repositories\Eloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Contract => concrete. Swapping a data source means editing one line here.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        Contracts\UserRepositoryInterface::class => Eloquent\UserRepository::class,
        Contracts\MenuRepositoryInterface::class => Eloquent\MenuRepository::class,
        Contracts\RoleRepositoryInterface::class => Eloquent\RoleRepository::class,
        Contracts\PermissionRepositoryInterface::class => Eloquent\PermissionRepository::class,
        Contracts\AuditLogRepositoryInterface::class => Eloquent\AuditLogRepository::class,
        Contracts\SettingRepositoryInterface::class => Eloquent\SettingRepository::class,
        Contracts\LanguageRepositoryInterface::class => Eloquent\LanguageRepository::class,
        Contracts\LearningModuleRepositoryInterface::class => Eloquent\LearningModuleRepository::class,
        Contracts\LessonRepositoryInterface::class => Eloquent\LessonRepository::class,
        Contracts\LessonItemRepositoryInterface::class => Eloquent\LessonItemRepository::class,
        Contracts\QuizRepositoryInterface::class => Eloquent\QuizRepository::class,
        Contracts\QuizAttemptRepositoryInterface::class => Eloquent\QuizAttemptRepository::class,
        Contracts\FlashcardDeckRepositoryInterface::class => Eloquent\FlashcardDeckRepository::class,
        Contracts\FlashcardRepositoryInterface::class => Eloquent\FlashcardRepository::class,
        Contracts\BookmarkRepositoryInterface::class => Eloquent\BookmarkRepository::class,
        Contracts\NoteRepositoryInterface::class => Eloquent\NoteRepository::class,
        Contracts\AchievementRepositoryInterface::class => Eloquent\AchievementRepository::class,
        Contracts\ProgressRepositoryInterface::class => Eloquent\ProgressRepository::class,
    ];
}
