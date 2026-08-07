<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;

class NoteData extends BaseData
{
    public function __construct(
        public readonly ?string $title,
        public readonly string $body,
        public readonly string $color,
        public readonly bool $isPinned,
        public readonly ?string $notableType,
        public readonly int|string|null $notableId,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new static(
            title: $request->input('title'),
            body: (string) $request->string('body'),
            color: (string) $request->input('color', 'yellow'),
            isPinned: $request->boolean('is_pinned'),
            notableType: $request->input('notable_type'),
            notableId: $request->input('notable_id'),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'color' => $this->color,
            'is_pinned' => $this->isPinned,
            'notable_type' => $this->notableType,
            'notable_id' => $this->notableId,
        ];
    }
}
