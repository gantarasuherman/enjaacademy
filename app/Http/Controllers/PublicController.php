<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function __construct(
        private readonly LanguageRepositoryInterface $languages,
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function home(): View
    {
        return view('public.home', [
            'languages' => $this->languages->withModuleCounts(),
            'featured' => $this->modules->featured(6),
            'leaderboard' => $this->users->leaderboard(5),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    /**
     * Language switcher available to guests and members alike.
     *
     * Named `switchLocale` rather than `locale` on purpose — ext-intl defines a
     * global `Locale` class, and Laravel would resolve the bare string to that
     * class instead of this method.
     */
    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        $supported = array_keys(config('app.supported_locales', ['id' => 'Indonesia', 'en' => 'English']));

        abort_unless(in_array($locale, $supported, true), 404);

        $request->session()->put('locale', $locale);

        $request->user()?->forceFill(['locale' => $locale])->saveQuietly();

        return back();
    }
}
