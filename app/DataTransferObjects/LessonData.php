<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class LessonData extends BaseData
{
    public function __construct(
        public readonly int $moduleId,
        public readonly string $title,
        public readonly ?string $slug,
        public readonly ?string $level,
        public readonly ?string $summary,
        public readonly ?string $content,
        public readonly ?string $translatedContent,
        public readonly ?string $videoUrl,
        public readonly int $estimatedMinutes,
        public readonly int $xpReward,
        public readonly int $sortOrder,
        public readonly bool $isPublished,
        public readonly ?UploadedFile $coverImage,
        public readonly ?UploadedFile $audio,
        /** @var array<int, array<string, mixed>> */
        public readonly array $items = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $title = (string) $request->string('title');

        return new static(
            moduleId: (int) $request->input('learning_module_id'),
            title: $title,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($title),
            level: $request->input('level'),
            summary: $request->input('summary'),
            content: $request->input('content'),
            translatedContent: $request->input('translated_content'),
            videoUrl: $request->input('video_url'),
            estimatedMinutes: (int) $request->input('estimated_minutes', 10),
            xpReward: (int) $request->input('xp_reward', 20),
            sortOrder: (int) $request->input('sort_order', 0),
            isPublished: $request->boolean('is_published'),
            coverImage: $request->file('cover_image'),
            audio: $request->file('audio'),
            items: (array) $request->input('items', []),
        );
    }

    public function toArray(): array
    {
        return [
            'learning_module_id' => $this->moduleId,
            'title' => $this->title,
            'slug' => $this->slug,
            'level' => $this->level,
            'summary' => $this->summary,
            'content' => $this->content,
            'translated_content' => $this->translatedContent,
            'video_url' => $this->videoUrl,
            'estimated_minutes' => $this->estimatedMinutes,
            'xp_reward' => $this->xpReward,
            'sort_order' => $this->sortOrder,
            'is_published' => $this->isPublished,
            'published_at' => $this->isPublished ? now() : null,
        ];
    }
}
