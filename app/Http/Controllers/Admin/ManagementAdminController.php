<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Services\Setting\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Site-wide settings: branding, contact details and SEO meta.
 */
class ManagementAdminController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function index(): View
    {
        return view('admin.management-admin.index', [
            'general' => $this->settings->group('general'),
            'meta' => $this->settings->group('meta'),
            'roles' => Role::orderBy('name')->pluck('name', 'name'),
        ]);
    }

    public function updateGeneral(SettingRequest $request): RedirectResponse
    {
        $values = $request->safe()->only([
            'app_name', 'app_tagline', 'contact_email', 'contact_phone',
            'address', 'maintenance_notice',
        ]);

        $values['registration_open'] = $request->boolean('registration_open');
        $values['default_role'] = (string) $request->input('default_role', 'Student');

        $this->settings->save($values, 'general');

        foreach (['logo', 'favicon'] as $key) {
            if ($request->hasFile($key)) {
                $this->settings->saveFile($key, $request->file($key), 'general');
            }
        }

        return back()->with('success', __('General settings saved.'));
    }

    public function updateMeta(SettingRequest $request): RedirectResponse
    {
        $this->settings->save($request->safe()->only([
            'meta_title', 'meta_description', 'meta_keywords', 'analytics_id',
        ]), 'meta');

        if ($request->hasFile('og_image')) {
            $this->settings->saveFile('og_image', $request->file('og_image'), 'meta');
        }

        return back()->with('success', __('Meta settings saved.'));
    }
}
