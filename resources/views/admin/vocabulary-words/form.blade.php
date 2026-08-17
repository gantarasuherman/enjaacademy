@extends('layouts.app')
@section('title', $word->exists ? __('Edit kata') : __('Kata baru'))

@section('content')
    <x-page-header
        :title="$word->exists ? __('Edit kata') : __('Kata baru')"
        :back="route('admin.vocabulary-words.index')"
        :back-label="__('Kembali ke kosakata')"
    />

    <form method="POST"
          action="{{ $word->exists ? route('admin.vocabulary-words.update', $word) : route('admin.vocabulary-words.store') }}"
          class="grid gap-6 lg:grid-cols-3"
          x-data="{
              examples: @js($examples->map(fn ($e) => [
                  'id' => $e->id,
                  'sentence_en' => $e->sentence_en,
                  'sentence_id' => $e->sentence_id,
              ])->values()),
              addExample() {
                  this.examples.push({ id: null, sentence_en: '', sentence_id: '' });
              },
              removeExample(index) {
                  this.examples.splice(index, 1);
              },

              aiLoading: false,
              aiMessage: null,
              async generateWithAi() {
                  const word = this.$refs.word.value.trim();

                  if (! word) return;

                  this.aiLoading = true;
                  this.aiMessage = null;

                  try {
                      const response = await window.post('{{ route('admin.vocabulary-words.ai.generate') }}', {
                          word,
                          language_id: this.$refs.language_id.value,
                          level: this.$refs.level.value,
                      });

                      if (! response.available || response.error) {
                          this.aiMessage = response.message;
                          return;
                      }

                      const data = response.data;
                      this.$refs.phonetic.value = data.phonetic;
                      this.$refs.part_of_speech.value = data.part_of_speech;
                      this.$refs.meaning_id.value = data.meaning_id;
                      this.$refs.meaning_en.value = data.meaning_en;
                      this.$refs.synonyms_text.value = data.synonyms.join(', ');
                      this.$refs.antonyms_text.value = data.antonyms.join(', ');
                      this.$refs.collocations_text.value = data.collocations.join(', ');

                      if (data.examples.length > 0) {
                          this.examples = data.examples.map((e) => ({ id: null, ...e }));
                      }
                  } catch {
                      this.aiMessage = '{{ __('Terjadi kesalahan menghubungi layanan AI.') }}';
                  } finally {
                      this.aiLoading = false;
                  }
              },
          }">
        @csrf
        @if ($word->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3 rounded-lg bg-brand-50 p-3 dark:bg-brand-500/10">
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        {{ __('Isi Bahasa, Level dan Kata di bawah, lalu klik tombol ini untuk mengisi sisa field (fonetik, arti, sinonim/antonim, kolokasi, contoh kalimat) otomatis. Hasilnya bisa diedit sebelum disimpan.') }}
                    </p>
                    <button type="button" @click="generateWithAi()" :disabled="aiLoading" class="btn-secondary shrink-0 text-sm disabled:opacity-50">
                        <x-icon name="sparkles" class="size-4" /> <span x-text="aiLoading ? '{{ __('Memproses…') }}' : '{{ __('Buat dengan AI') }}'"></span>
                    </button>
                </div>
                <p x-show="aiMessage" x-cloak class="mb-4 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="aiMessage"></p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="language_id" class="label">{{ __('Bahasa') }} <span class="text-rose-500">*</span></label>
                        <select id="language_id" name="language_id" x-ref="language_id" required class="input">
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}" @selected(old('language_id', $word->language_id) == $language->id)>{{ $language->name }}</option>
                            @endforeach
                        </select>
                        @error('language_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="level" class="label">{{ __('Level') }} <span class="text-rose-500">*</span></label>
                        <select id="level" name="level" x-ref="level" required class="input">
                            @foreach ($levelsByLanguage as $langSlug => $levelGroup)
                                <optgroup label="{{ ucfirst($langSlug) }}">
                                    @foreach ($levelGroup as $level)
                                        <option value="{{ $level }}" @selected(old('level', $word->level) === $level)>{{ $level }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('level') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="help">{{ __('Skala CEFR untuk Inggris, JLPT (N5–N1) untuk Jepang.') }}</p>
                    </div>

                    <div>
                        <label for="word" class="label">{{ __('Kata / frasa') }} <span class="text-rose-500">*</span></label>
                        <input id="word" name="word" x-ref="word" value="{{ old('word', $word->word) }}" required
                               class="input @error('word') border-rose-400 @enderror" placeholder="ambiguous">
                        @error('word') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phonetic" class="label">{{ __('Fonetik (IPA)') }}</label>
                        <input id="phonetic" name="phonetic" x-ref="phonetic" value="{{ old('phonetic', $word->phonetic) }}"
                               class="input font-mono text-sm" placeholder="/æmˈbɪɡjuəs/">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="part_of_speech" class="label">{{ __('Jenis kata') }}</label>
                        <input id="part_of_speech" name="part_of_speech" x-ref="part_of_speech" value="{{ old('part_of_speech', $word->part_of_speech) }}"
                               class="input" placeholder="adjective">
                    </div>

                    <div>
                        <label for="meaning_id" class="label">{{ __('Arti (Indonesia)') }} <span class="text-rose-500">*</span></label>
                        <textarea id="meaning_id" name="meaning_id" x-ref="meaning_id" rows="2" required
                                  class="input @error('meaning_id') border-rose-400 @enderror">{{ old('meaning_id', $word->meaning_id) }}</textarea>
                        @error('meaning_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="meaning_en" class="label">{{ __('Arti (Inggris)') }}</label>
                        <textarea id="meaning_en" name="meaning_en" x-ref="meaning_en" rows="2"
                                  class="input @error('meaning_en') border-rose-400 @enderror">{{ old('meaning_en', $word->meaning_en) }}</textarea>
                        @error('meaning_en') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="help">{{ __('Hanya untuk kata Inggris — kosongkan untuk kata Jepang.') }}</p>
                    </div>

                    <div>
                        <label for="synonyms_text" class="label">{{ __('Sinonim') }}</label>
                        <input id="synonyms_text" name="synonyms_text" x-ref="synonyms_text"
                               value="{{ old('synonyms_text', implode(', ', $word->synonyms ?? [])) }}"
                               class="input" placeholder="{{ __('pisahkan dengan koma') }}">
                    </div>

                    <div>
                        <label for="antonyms_text" class="label">{{ __('Antonim') }}</label>
                        <input id="antonyms_text" name="antonyms_text" x-ref="antonyms_text"
                               value="{{ old('antonyms_text', implode(', ', $word->antonyms ?? [])) }}"
                               class="input" placeholder="{{ __('pisahkan dengan koma') }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="collocations_text" class="label">{{ __('Kolokasi') }}</label>
                        <input id="collocations_text" name="collocations_text" x-ref="collocations_text"
                               value="{{ old('collocations_text', implode(', ', $word->collocations ?? [])) }}"
                               class="input" placeholder="{{ __('pisahkan dengan koma') }}">
                        <p class="help">{{ __('Contoh: take a decision, make a decision') }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">{{ __('Contoh Kalimat') }}</h2>
                    <button type="button" @click="addExample()" class="btn-secondary text-sm">
                        <x-icon name="plus" class="size-4" /> {{ __('Tambah Contoh') }}
                    </button>
                </div>

                <p x-show="examples.length === 0" x-cloak class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-sm text-slate-400 dark:border-slate-700">
                    {{ __('Minimal 2 contoh kalimat direkomendasikan — dipakai juga untuk soal isian Kuis Harian.') }}
                </p>

                <div class="space-y-3">
                    <template x-for="(example, index) in examples" :key="index">
                        <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-700" x-text="`{{ __('Contoh') }} #${index + 1}`"></span>
                                <button type="button" @click="removeExample(index)" class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                            </div>

                            <input type="hidden" :name="`examples[${index}][id]`" :value="example.id">

                            <div class="grid gap-2 sm:grid-cols-2">
                                <div>
                                    <label class="label text-xs">{{ __('Kalimat (bahasa kata ini)') }}</label>
                                    <input type="text" :name="`examples[${index}][sentence_en]`" x-model="example.sentence_en" class="input text-sm">
                                </div>
                                <div>
                                    <label class="label text-xs">{{ __('Terjemahan Indonesia') }}</label>
                                    <input type="text" :name="`examples[${index}][sentence_id]`" x-model="example.sentence_id" class="input text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <p class="text-sm text-slate-500">
                    {{ __('Kata ini otomatis masuk pool soal Kuis Harian (Pilihan Ganda, Isian, Cocokkan, Benar/Salah, Konteks) untuk levelnya.') }}
                </p>

                <div class="mt-5 flex flex-col gap-2">
                    <button type="submit" class="btn-primary">{{ $word->exists ? __('Simpan') : __('Buat kata') }}</button>
                    <a href="{{ route('admin.vocabulary-words.index') }}" class="btn-secondary text-center">{{ __('Batal') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
