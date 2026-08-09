/**
 * Item "type" is a display concept only — it lives at `item.extra.type` on
 * the wire (matching the precedent already set by Reading's glossary rows,
 * which use `extra: {type: 'glossary'}`). There is no `type` column on
 * lesson_items; every type here is just a curated subset of the same six
 * generic columns (term/reading/romaji/meaning/example/example_meaning)
 * plus, for kanji only, two extra sub-fields (onyomi/kunyomi).
 *
 * `item.type` is only ever assigned in one place (lesson-builder.js's
 * saveDrawer): when the admin explicitly picks a pill in the drawer. An
 * item whose drawer is never opened keeps whatever `extra.type` string it
 * was loaded with — including values this builder doesn't recognise, like
 * `'glossary'` — so editing unrelated fields never overwrites it.
 */

export const ITEM_TYPES = [
    { value: 'kana', label: 'Kana' },
    { value: 'kanji', label: 'Kanji' },
    { value: 'kosakata', label: 'Kosakata' },
    { value: 'grammar', label: 'Grammar' },
    { value: 'kalimat', label: 'Kalimat' },
    { value: 'custom', label: 'Custom' },
];

const KNOWN_TYPES = ITEM_TYPES.map((type) => type.value);

/** Field template per type: which generic/extra fields the drawer renders. */
export const TEMPLATE_FIELDS = {
    kana: [
        { key: 'term', label: 'Kana', target: 'term', placeholder: 'あ' },
        { key: 'romaji', label: 'Romaji', target: 'romaji', placeholder: 'a' },
        { key: 'example', label: 'Contoh', target: 'example', placeholder: 'あさ' },
        { key: 'example_meaning', label: 'Arti', target: 'example_meaning', placeholder: 'pagi' },
    ],
    kanji: [
        { key: 'term', label: 'Kanji', target: 'term', placeholder: '学' },
        { key: 'onyomi', label: 'Onyomi', target: 'extra.onyomi', placeholder: 'がく' },
        { key: 'kunyomi', label: 'Kunyomi', target: 'extra.kunyomi', placeholder: 'まなぶ' },
        { key: 'meaning', label: 'Arti', target: 'meaning', placeholder: 'belajar' },
        { key: 'example', label: 'Contoh', target: 'example', placeholder: '学生' },
        { key: 'example_meaning', label: 'Arti contoh', target: 'example_meaning', placeholder: 'siswa' },
    ],
    kosakata: [
        { key: 'term', label: 'Kosakata', target: 'term', placeholder: '先生' },
        { key: 'reading', label: 'Furigana', target: 'reading', placeholder: 'せんせい' },
        { key: 'romaji', label: 'Romaji', target: 'romaji', placeholder: 'sensei' },
        { key: 'meaning', label: 'Arti', target: 'meaning', placeholder: 'guru' },
        { key: 'example', label: 'Contoh', target: 'example', placeholder: '先生は学校にいます。' },
        { key: 'example_meaning', label: 'Arti contoh', target: 'example_meaning', placeholder: 'Guru berada di sekolah.' },
    ],
    grammar: [
        { key: 'term', label: 'Pola', target: 'term', placeholder: '～です' },
        { key: 'meaning', label: 'Penjelasan', target: 'meaning', multiline: true, placeholder: 'Digunakan untuk...' },
        { key: 'example', label: 'Contoh', target: 'example', placeholder: '私は学生です。' },
        { key: 'example_meaning', label: 'Arti', target: 'example_meaning', placeholder: 'Saya adalah siswa.' },
    ],
    kalimat: [
        { key: 'term', label: 'Kalimat', target: 'term', multiline: true },
        { key: 'meaning', label: 'Arti', target: 'meaning', multiline: true },
    ],
    custom: [
        { key: 'term', label: 'Term (wajib)', target: 'term' },
        { key: 'reading', label: 'Bacaan / furigana', target: 'reading' },
        { key: 'romaji', label: 'Romaji / fonetik', target: 'romaji' },
        { key: 'meaning', label: 'Arti', target: 'meaning' },
        { key: 'example', label: 'Contoh kalimat', target: 'example' },
        { key: 'example_meaning', label: 'Arti contoh', target: 'example_meaning' },
    ],
};

/**
 * Which template's field set to render for a given type string. Falls back
 * to Custom (the lossless raw-field layout) for anything this builder
 * doesn't recognise — without ever renaming the stored type itself.
 */
export function templateFor(type) {
    return KNOWN_TYPES.includes(type) ? type : 'custom';
}

/**
 * Default type for an item that predates this builder (no `extra.type`
 * set yet) — inferred from its parent module's content_type. Anything that
 * doesn't map cleanly (conversation/listening/speaking/reading/writing/exam)
 * falls back to Custom, which shows every raw field losslessly.
 */
export function inferType(rawItem, moduleContentType) {
    if (rawItem?.extra?.type) return rawItem.extra.type;

    const fallback = { kana: 'kana', kanji: 'kanji', vocabulary: 'kosakata', grammar: 'grammar' };

    return fallback[moduleContentType] ?? 'custom';
}

/**
 * Kana/kanji have no dedicated "reading" field in their drawer (the term
 * itself, or kunyomi/onyomi, already carries it) — mirror it into `reading`
 * on save so the generic `reading` column stays populated for anything
 * that reads it directly (admin exports, older views). No-op for every
 * other template.
 */
export function applyReadingMirror(item) {
    const type = templateFor(item.type);

    if (type === 'kana') {
        item.reading = item.term;
    } else if (type === 'kanji') {
        item.reading = item.extra?.kunyomi || item.extra?.onyomi || '';
    }
}
