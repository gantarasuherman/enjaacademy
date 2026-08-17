<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('management-admin.update');
    }

    public function rules(): array
    {
        return [
            // General
            'app_name' => ['nullable', 'string', 'max:120'],
            'app_tagline' => ['nullable', 'string', 'max:200'],
            'contact_email' => ['nullable', 'email', 'max:180'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'registration_open' => ['boolean'],
            'maintenance_notice' => ['nullable', 'string', 'max:500'],

            // Meta / SEO
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'meta_keywords' => ['nullable', 'string', 'max:300'],
            'og_image' => ['nullable', 'image', 'max:2048'],
            'analytics_id' => ['nullable', 'string', 'max:60'],

            // Integrations
            'ai_provider' => ['nullable', 'in:gemini,grok,groq'],
            'gemini_api_key' => ['nullable', 'string', 'max:200'],
            'gemini_model' => ['nullable', 'string', 'max:100'],
            'clear_gemini_api_key' => ['boolean'],

            'grok_api_key' => ['nullable', 'string', 'max:200'],
            'grok_model' => ['nullable', 'string', 'max:100'],
            'clear_grok_api_key' => ['boolean'],

            'groq_api_key' => ['nullable', 'string', 'max:200'],
            'groq_model' => ['nullable', 'string', 'max:100'],
            'clear_groq_api_key' => ['boolean'],

            'tripay_merchant_code' => ['nullable', 'string', 'max:100'],
            'tripay_api_key' => ['nullable', 'string', 'max:200'],
            'tripay_private_key' => ['nullable', 'string', 'max:200'],
            'clear_tripay_api_key' => ['boolean'],
            'clear_tripay_private_key' => ['boolean'],
        ];
    }
}
