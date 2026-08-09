<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Auditable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'phone', 'avatar', 'bio',
        'birth_date', 'gender', 'locale', 'timezone', 'is_active', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    /* -----------------------------------------------------------------
     | Relations
     | -----------------------------------------------------------------
     */

    public function stat(): HasOne
    {
        return $this->hasOne(UserStat::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function xpLogs(): HasMany
    {
        return $this->hasMany(XpLog::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function flashcardDecks(): HasMany
    {
        return $this->hasMany(FlashcardDeck::class);
    }

    public function flashcardReviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
            ->withPivot(['progress', 'unlocked_at'])
            ->withTimestamps();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /* -----------------------------------------------------------------
     | Scopes
     | -----------------------------------------------------------------
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('username', 'like', "%{$term}%");
        }));
    }

    public function scopeWithRole(Builder $query, ?string $role): Builder
    {
        return $query->when($role, fn (Builder $q) => $q->whereHas('roles', fn ($r) => $r->where('name', $role)));
    }

    /* -----------------------------------------------------------------
     | Helpers
     | -----------------------------------------------------------------
     */

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('admin.super_role'));
    }

    /**
     * Entry to the admin shell requires a permission from one of the panel's
     * own modules. Holding only learner permissions (hiragana.view and the
     * like) is not enough — otherwise every student would reach the panel.
     * Each page still enforces its own `permission:` middleware on top.
     */
    public function canAccessAdminPanel(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $panelModules = config('admin.panel_modules', []);

        return $this->getAllPermissions()
            ->contains(fn ($permission) => in_array(
                str($permission->name)->before('.')->toString(),
                $panelModules,
                true,
            ));
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?background=4f46e5&color=fff&name='.urlencode($this->name);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->take(2)
            ->map(fn (string $part) => mb_substr($part, 0, 1))
            ->implode('');
    }

    /**
     * Cached role-name signature. Menu caching keys off this so every user
     * sharing a role set also shares a single cache entry.
     */
    public function roleSignature(): string
    {
        $roles = $this->getRoleNames()->sort()->values()->implode('|');

        return $roles === '' ? 'norole' : md5($roles);
    }
}
