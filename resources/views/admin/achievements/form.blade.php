@extends('layouts.app')
@section('title', $achievement->exists ? __('Edit pencapaian') : __('Pencapaian baru'))

@section('content')
    <x-page-header
        :title="$achievement->exists ? __('Edit pencapaian') : __('Pencapaian baru')"
        :description="__('Kriteria dievaluasi otomatis setiap kali XP peserta bertambah.')"
        :back="route('admin.achievements.index')"
        :back-label="__('Kembali ke pencapaian')"
    />

    <form method="POST"
          action="{{ $achievement->exists ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}"
          class="max-w-3xl space-y-6">
        @csrf
        @if ($achievement->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">{{ __('Nama') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $achievement->name) }}" required
                           class="input @error('name') border-rose-400 @enderror">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="label">{{ __('Deskripsi') }}</label>
                    <textarea id="description" name="description" rows="2" class="input">{{ old('description', $achievement->description) }}</textarea>
                </div>

                <div>
                    <label for="icon" class="label">{{ __('Ikon') }}</label>
                    <input id="icon" name="icon" value="{{ old('icon', $achievement->icon) }}" class="input" placeholder="trophy / 🔥">
                </div>

                <div>
                    <label for="badge_color" class="label">{{ __('Warna lencana') }}</label>
                    <select id="badge_color" name="badge_color" class="input">
                        @foreach (config('admin.menu.badge_colors') as $color)
                            <option value="{{ $color }}" @selected(old('badge_color', $achievement->badge_color ?? 'amber') === $color)>{{ $color }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="criteria_type" class="label">{{ __('Kriteria') }} <span class="text-rose-500">*</span></label>
                    <select id="criteria_type" name="criteria_type" required class="input">
                        @foreach ([
                            'xp_total' => __('Total XP'),
                            'level' => __('Level'),
                            'lessons_completed' => __('Materi selesai'),
                            'quizzes_completed' => __('Kuis selesai'),
                            'perfect_quizzes' => __('Nilai sempurna'),
                            'streak_days' => __('Hari beruntun'),
                            'flashcards_reviewed' => __('Flashcard direview'),
                            'manual' => __('Manual (diberikan admin)'),
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('criteria_type', $achievement->criteria_type ?? 'xp_total') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="criteria_value" class="label">{{ __('Ambang batas') }} <span class="text-rose-500">*</span></label>
                    <input id="criteria_value" name="criteria_value" type="number" min="0" required
                           value="{{ old('criteria_value', $achievement->criteria_value ?? 0) }}" class="input">
                    <p class="help">{{ __('Nilai minimum agar lencana terbuka.') }}</p>
                </div>

                <div>
                    <label for="xp_reward" class="label">{{ __('Bonus XP') }}</label>
                    <input id="xp_reward" name="xp_reward" type="number" min="0"
                           value="{{ old('xp_reward', $achievement->xp_reward ?? 0) }}" class="input">
                </div>

                <div>
                    <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                    <input id="sort_order" name="sort_order" type="number" min="0"
                           value="{{ old('sort_order', $achievement->sort_order ?? 0) }}" class="input">
                </div>

                <div class="flex items-end pb-2 sm:col-span-2">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $achievement->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Aktif') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.achievements.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">{{ $achievement->exists ? __('Simpan') : __('Buat pencapaian') }}</button>
        </div>
    </form>
@endsection
