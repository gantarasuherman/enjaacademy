<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class LessonItemData extends BaseData
{
    public function __construct(
        public readonly int $lessonId,
        public readonly string $term,
        public readonly ?string $reading,
        public readonly ?string $romaji,
        public readonly ?string $meaning,
        public readonly ?string $example,
        public readonly ?string $exampleMeaning,
        public readonly array $extra,
        public readonly int $sortOrder,
        public readonly bool $isActive,
        public readonly ?UploadedFile $audio,
        public readonly ?UploadedFile $image,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new static(
            lessonId: (int) $request->input('lesson_id'),
            term: (string) $request->string('term'),
            reading: $request->input('reading'),
            romaji: $request->input('romaji'),
            meaning: $request->input('meaning'),
            example: $request->input('example'),
            exampleMeaning: $request->input('example_meaning'),
            extra: array_filter((array) $request->input('extra', []), fn ($v) => $v !== null && $v !== ''),
            sortOrder: (int) $request->input('sort_order', 0),
            isActive: $request->boolean('is_active', true),
            audio: $request->file('audio'),
            image: $request->file('image'),
        );
    }

    public function toArray(): array
    {
        return [
            'lesson_id' => $this->lessonId,
            'term' => $this->term,
            'reading' => $this->reading,
            'romaji' => $this->romaji,
            'meaning' => $this->meaning,
            'example' => $this->example,
            'example_meaning' => $this->exampleMeaning,
            'extra' => $this->extra ?: null,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}
