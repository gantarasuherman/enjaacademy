<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\VocabularyWord;
use Illuminate\Foundation\Http\FormRequest;

class VocabularyWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $word = $this->route('vocabulary_word');

        return $word instanceof VocabularyWord
            ? $this->user()->can('update', $word)
            : $this->user()->can('create', VocabularyWord::class);
    }

    public function rules(): array
    {
        return [
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'word' => ['required', 'string', 'max:255'],
            'phonetic' => ['nullable', 'string', 'max:255'],
            'part_of_speech' => ['nullable', 'string', 'max:40'],
            'meaning_id' => ['required', 'string'],
            // Only meaningful for English-target words ("what this English word
            // means, in English") — Japanese entries have no such field.
            'meaning_en' => ['nullable', 'string'],
            'level' => ['required', 'in:Beginner,Elementary,Intermediate,Upper-Intermediate,Advanced,N5,N4,N3,N2,N1'],
            'synonyms_text' => ['nullable', 'string'],
            'antonyms_text' => ['nullable', 'string'],
            'collocations_text' => ['nullable', 'string'],

            'examples' => ['array'],
            'examples.*.id' => ['nullable', 'integer'],
            'examples.*.sentence_en' => ['nullable', 'string'],
            'examples.*.sentence_id' => ['nullable', 'string'],
        ];
    }

    /** Comma-separated textareas from the form become the JSON arrays the model actually stores. */
    public function toWordAttributes(): array
    {
        $data = $this->safe()->except(['synonyms_text', 'antonyms_text', 'collocations_text', 'examples']);

        $data['synonyms'] = $this->splitList($this->input('synonyms_text'));
        $data['antonyms'] = $this->splitList($this->input('antonyms_text'));
        $data['collocations'] = $this->splitList($this->input('collocations_text'));

        return $data;
    }

    private function splitList(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();
    }
}
