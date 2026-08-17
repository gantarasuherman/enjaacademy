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
            'integrations' => [
                'ai_provider' => $this->settings->get('ai_provider', 'gemini'),
                // The key itself is never sent to the browser, only whether one is set.
                'gemini_api_key_set' => $this->settings->hasSecret('gemini_api_key'),
                'gemini_model' => $this->settings->get('gemini_model', config('services.gemini.model')),
                'grok_api_key_set' => $this->settings->hasSecret('grok_api_key'),
                'grok_model' => $this->settings->get('grok_model', config('services.grok.model')),
                'groq_api_key_set' => $this->settings->hasSecret('groq_api_key'),
                'groq_model' => $this->settings->get('groq_model', config('services.groq.model')),
                'tripay_merchant_code' => $this->settings->get('tripay_merchant_code', config('services.tripay.merchant_code')),
                'tripay_api_key_set' => $this->settings->hasSecret('tripay_api_key'),
                'tripay_private_key_set' => $this->settings->hasSecret('tripay_private_key'),
            ],
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

    public function updateIntegrations(SettingRequest $request): RedirectResponse
    {
        $this->settings->save([
            'ai_provider' => in_array($request->input('ai_provider'), ['grok', 'groq'], true) ? $request->input('ai_provider') : 'gemini',
            'gemini_model' => $request->input('gemini_model') ?: 'gemini-flash-latest',
            'grok_model' => $request->input('grok_model') ?: 'grok-4.6',
            'groq_model' => $request->input('groq_model') ?: 'openai/gpt-oss-120b',
            'tripay_merchant_code' => $request->input('tripay_merchant_code'),
        ], 'integrations');

        if ($request->boolean('clear_gemini_api_key')) {
            $this->settings->clearSecret('gemini_api_key', 'integrations');
        } else {
            $this->settings->saveSecret('gemini_api_key', $request->input('gemini_api_key'), 'integrations');
        }

        if ($request->boolean('clear_grok_api_key')) {
            $this->settings->clearSecret('grok_api_key', 'integrations');
        } else {
            $this->settings->saveSecret('grok_api_key', $request->input('grok_api_key'), 'integrations');
        }

        if ($request->boolean('clear_groq_api_key')) {
            $this->settings->clearSecret('groq_api_key', 'integrations');
        } else {
            $this->settings->saveSecret('groq_api_key', $request->input('groq_api_key'), 'integrations');
        }

        if ($request->boolean('clear_tripay_api_key')) {
            $this->settings->clearSecret('tripay_api_key', 'integrations');
        } else {
            $this->settings->saveSecret('tripay_api_key', $request->input('tripay_api_key'), 'integrations');
        }

        if ($request->boolean('clear_tripay_private_key')) {
            $this->settings->clearSecret('tripay_private_key', 'integrations');
        } else {
            $this->settings->saveSecret('tripay_private_key', $request->input('tripay_private_key'), 'integrations');
        }

        return back()->with('success', __('Integrasi disimpan.'));
    }
}
