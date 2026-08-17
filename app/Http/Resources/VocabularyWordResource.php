<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VocabularyWordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'word' => $this->word,
            'phonetic' => $this->phonetic,
            'partOfSpeech' => $this->part_of_speech,
            'meaningId' => $this->meaning_id,
            'meaningEn' => $this->meaning_en,
            'level' => $this->level,
            'synonyms' => $this->synonyms ?? [],
            'antonyms' => $this->antonyms ?? [],
            'collocations' => $this->collocations ?? [],
            'examples' => $this->whenLoaded('examples', fn () => $this->examples->map(fn ($e) => [
                'sentenceEn' => $e->sentence_en,
                'sentenceId' => $e->sentence_id,
            ])),
        ];
    }
}
