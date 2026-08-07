<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /** @var array<int, array{group:string, key:string, value:?string, type:string, label:string}> */
    private array $settings = [
        ['group' => 'general', 'key' => 'app_name', 'value' => 'Nihongo & English Academy', 'type' => 'string', 'label' => 'Application name'],
        ['group' => 'general', 'key' => 'app_tagline', 'value' => 'Belajar bahasa Jepang & Inggris dari nol sampai mahir', 'type' => 'string', 'label' => 'Tagline'],
        ['group' => 'general', 'key' => 'contact_email', 'value' => 'halo@nihongo.test', 'type' => 'string', 'label' => 'Contact email'],
        ['group' => 'general', 'key' => 'contact_phone', 'value' => '+62 812-0000-0000', 'type' => 'string', 'label' => 'Contact phone'],
        ['group' => 'general', 'key' => 'address', 'value' => 'Jakarta, Indonesia', 'type' => 'string', 'label' => 'Address'],
        ['group' => 'general', 'key' => 'registration_open', 'value' => '1', 'type' => 'bool', 'label' => 'Public registration open'],
        ['group' => 'general', 'key' => 'default_role', 'value' => 'Student', 'type' => 'string', 'label' => 'Role assigned on sign-up'],
        ['group' => 'general', 'key' => 'maintenance_notice', 'value' => null, 'type' => 'string', 'label' => 'Maintenance banner'],

        ['group' => 'meta', 'key' => 'meta_title', 'value' => 'Nihongo & English Academy — Belajar Bahasa Jepang & Inggris', 'type' => 'string', 'label' => 'Meta title'],
        ['group' => 'meta', 'key' => 'meta_description', 'value' => 'Platform belajar bahasa Jepang dan Inggris: hiragana, katakana, kanji, JLPT, TOEFL, IELTS, dengan kuis, flashcard, dan pelacakan progres.', 'type' => 'string', 'label' => 'Meta description'],
        ['group' => 'meta', 'key' => 'meta_keywords', 'value' => 'belajar bahasa jepang, hiragana, katakana, kanji, jlpt, bahasa inggris, toefl, ielts', 'type' => 'string', 'label' => 'Meta keywords'],
        ['group' => 'meta', 'key' => 'analytics_id', 'value' => null, 'type' => 'string', 'label' => 'Analytics measurement ID'],
    ];

    public function run(): void
    {
        foreach ($this->settings as $index => $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting + ['sort_order' => $index, 'is_public' => $setting['group'] === 'meta'],
            );
        }

        app(\App\Services\Setting\SettingService::class)->forget();
    }
}
