<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VocabularyWordRequest;
use App\Models\Language;
use App\Models\VocabularyWord;
use App\Models\VocabularyWordExample;
use App\Services\AI\AiErrorTranslator;
use App\Services\AI\VocabularyAiService;
use App\Services\System\ImportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class VocabularyWordController extends Controller
{
    /** Keyed by language slug so the level <select> can group options and the admin never picks a mismatched scale. */
    private const LEVELS_BY_LANGUAGE = [
        'english' => ['Beginner', 'Elementary', 'Intermediate', 'Upper-Intermediate', 'Advanced'],
        'japanese' => ['N5', 'N4', 'N3', 'N2', 'N1'],
    ];

    public function __construct(
        private readonly ImportExportService $io,
        private readonly VocabularyAiService $ai,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', VocabularyWord::class);

        $words = VocabularyWord::query()
            ->with('language')
            ->when($request->filled('language'), fn ($q) => $q->where('language_id', $request->integer('language')))
            ->forLevel($request->string('level')->toString() ?: null)
            ->search($request->string('search')->toString() ?: null)
            ->orderByDesc('id')
            ->paginate($this->perPage())
            ->withQueryString();

        return view('admin.vocabulary-words.index', [
            'words' => $words,
            'languages' => Language::orderBy('name')->get(),
            'levelsByLanguage' => self::LEVELS_BY_LANGUAGE,
            'totalsByLanguage' => VocabularyWord::query()
                ->join('languages', 'languages.id', '=', 'vocabulary_words.language_id')
                ->selectRaw('languages.name, count(*) as total')
                ->groupBy('languages.name')
                ->pluck('total', 'name'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', VocabularyWord::class);

        return view('admin.vocabulary-words.form', [
            'word' => new VocabularyWord,
            'examples' => collect(),
            'languages' => Language::orderBy('name')->get(),
            'levelsByLanguage' => self::LEVELS_BY_LANGUAGE,
        ]);
    }

    public function store(VocabularyWordRequest $request): RedirectResponse
    {
        $word = DB::transaction(function () use ($request) {
            $word = VocabularyWord::create($request->toWordAttributes());
            $this->syncExamples($word, $request->input('examples', []));

            return $word;
        });

        return redirect()->route('admin.vocabulary-words.edit', $word)
            ->with('success', __('Kata ":word" berhasil dibuat.', ['word' => $word->word]));
    }

    public function edit(VocabularyWord $vocabularyWord): View
    {
        $this->authorize('update', $vocabularyWord);

        return view('admin.vocabulary-words.form', [
            'word' => $vocabularyWord,
            'examples' => $vocabularyWord->examples,
            'languages' => Language::orderBy('name')->get(),
            'levelsByLanguage' => self::LEVELS_BY_LANGUAGE,
        ]);
    }

    public function update(VocabularyWordRequest $request, VocabularyWord $vocabularyWord): RedirectResponse
    {
        DB::transaction(function () use ($request, $vocabularyWord) {
            $vocabularyWord->update($request->toWordAttributes());
            $this->syncExamples($vocabularyWord, $request->input('examples', []));
        });

        return back()->with('success', __('Kata ":word" berhasil diperbarui.', ['word' => $vocabularyWord->word]));
    }

    public function destroy(VocabularyWord $vocabularyWord): RedirectResponse
    {
        $this->authorize('delete', $vocabularyWord);

        $vocabularyWord->delete();

        return redirect()->route('admin.vocabulary-words.index')
            ->with('success', __('Kata ":word" berhasil dihapus.', ['word' => $vocabularyWord->word]));
    }

    /** Checkbox-select delete from the index table — one row or "select all" on the current page. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('vocabulary-words.delete'), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:vocabulary_words,id'],
        ]);

        $count = VocabularyWord::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.vocabulary-words.index')
            ->with('success', __(':count kata berhasil dihapus.', ['count' => $count]));
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorize('import', VocabularyWord::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:8192'],
        ]);

        $result = $this->io->importVocabularyWords($request->file('file'));

        $message = $result['skipped'] > 0
            ? __(':imported kata berhasil diimpor, :skipped duplikat dilewati.', $result)
            : __(':imported kata berhasil diimpor.', $result);

        return back()->with('success', $message);
    }

    public function template(): StreamedResponse
    {
        $this->authorize('import', VocabularyWord::class);

        return $this->io->vocabularyWordTemplate();
    }

    /**
     * Fills the rest of the form (phonetic, meanings, synonyms/antonyms/
     * collocations, example sentences) from just the word/language/level the
     * admin already typed — used by both "Tambah kata" and "Edit kata".
     */
    public function generateWithAi(Request $request): JsonResponse
    {
        abort_unless($request->user()->canAny(['vocabulary-words.create', 'vocabulary-words.update']), 403);

        if (! $this->ai->available()) {
            return response()->json([
                'available' => false,
                'message' => __('Fitur AI belum diaktifkan. Hubungi admin untuk mengatur API key.'),
            ]);
        }

        $validated = $request->validate([
            'word' => ['required', 'string', 'max:255'],
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'level' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $language = Language::findOrFail($validated['language_id']);
            $data = $this->ai->generateWord($validated['word'], $language->slug, $validated['level'] ?? null);

            return response()->json(['available' => true, 'data' => $data]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'available' => true,
                'error' => true,
                'message' => AiErrorTranslator::describe($e),
            ]);
        }
    }

    /**
     * Wholesale replace, mirroring GrammarPatternController::syncItems() — the
     * form always posts the whole example list on a real save. An empty
     * array means "no `examples` field was submitted at all" (e.g. a partial
     * API-style request), not "delete every example" — so it's a no-op
     * rather than wiping existing rows.
     */
    private function syncExamples(VocabularyWord $word, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $keptIds = [];

        foreach (array_values($rows) as $index => $row) {
            if (blank($row['sentence_en'] ?? null)) {
                continue;
            }

            $example = (! empty($row['id']))
                ? $word->examples()->find($row['id']) ?? new VocabularyWordExample(['vocabulary_word_id' => $word->id])
                : new VocabularyWordExample(['vocabulary_word_id' => $word->id]);

            $example->fill([
                'sentence_en' => $row['sentence_en'],
                'sentence_id' => $row['sentence_id'] ?? null,
                'sort_order' => $index,
            ])->save();

            $keptIds[] = $example->id;
        }

        $word->examples()->whereNotIn('id', $keptIds ?: [0])->delete();
    }
}
