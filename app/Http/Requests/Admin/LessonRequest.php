<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');

        return $lesson instanceof Lesson
            ? $this->user()->can('update', $lesson)
            : $this->user()->can('create', Lesson::class);
    }

    public function rules(): array
    {
        $id = $this->route('lesson')?->id;

        return [
            'learning_module_id' => ['required', 'integer', Rule::exists('learning_modules', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('lessons', 'slug')->ignore($id)],
            'level' => ['nullable', 'string', 'max:20'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:20480'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'xp_reward' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],

            'items' => ['array'],
            'items.*.id' => ['nullable', 'integer', Rule::exists('lesson_items', 'id')],
            'items.*.term' => ['required_with:items', 'string', 'max:255'],
            'items.*.reading' => ['nullable', 'string', 'max:255'],
            'items.*.romaji' => ['nullable', 'string', 'max:255'],
            'items.*.meaning' => ['nullable', 'string', 'max:500'],
            'items.*.example' => ['nullable', 'string', 'max:1000'],
            'items.*.example_meaning' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
