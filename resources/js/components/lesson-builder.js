import Sortable from 'sortablejs';
import { ITEM_TYPES, TEMPLATE_FIELDS, templateFor, inferType, applyReadingMirror } from './lesson-builder/item-types.js';
import { parsePastedText } from './lesson-builder/bulk-paste.js';
import { draftKeyFor, readDraft, writeDraft, clearDraft, debounce } from './lesson-builder/autosave.js';

function uid() {
    return typeof crypto !== 'undefined' && crypto.randomUUID
        ? crypto.randomUUID()
        : `${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

/** Turns a title into a URL-safe slug — mirrors Str::slug() closely enough for a live preview. */
function slugify(value) {
    return (value ?? '')
        .toString()
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function hydrateItem(raw, moduleContentType) {
    return {
        _uid: uid(),
        id: raw.id ?? null,
        term: raw.term ?? '',
        reading: raw.reading ?? '',
        romaji: raw.romaji ?? '',
        meaning: raw.meaning ?? '',
        example: raw.example ?? '',
        example_meaning: raw.example_meaning ?? '',
        extra: raw.extra ?? {},
        type: inferType(raw, moduleContentType),
    };
}

function blankItem(type, moduleContentType) {
    return {
        _uid: uid(),
        id: null,
        term: '',
        reading: '',
        romaji: '',
        meaning: '',
        example: '',
        example_meaning: '',
        extra: {},
        type: type ?? inferType({}, moduleContentType),
    };
}

export default function lessonBuilder({
    lesson = {},
    items = [],
    moduleContentType = null,
    lessonExists = false,
    draftKey = 'new',
    justSaved = false,
} = {}) {
    return {
        // ---- core fields (all posted via name="..." inputs, x-model just keeps them reactive) ----
        title: lesson.title ?? '',
        slug: lesson.slug ?? '',
        slugTouched: Boolean(lesson.slug),
        moduleId: lesson.learning_module_id ?? null,
        level: lesson.level ?? '',
        summary: lesson.summary ?? '',
        content: lesson.content ?? '',
        translatedContent: lesson.translated_content ?? '',
        videoUrl: lesson.video_url ?? '',
        estimatedMinutes: lesson.estimated_minutes ?? 10,
        xpReward: lesson.xp_reward ?? 20,
        sortOrder: lesson.sort_order ?? 0,
        isPublished: Boolean(lesson.is_published),

        items: items.map((raw) => hydrateItem(raw, moduleContentType)),
        moduleContentType,
        lessonExists,

        // ---- item list pagination (display only — every item still renders
        // its hidden inputs so the full set is always submitted; see the
        // `x-show` on `.lesson-item-card` in form.blade.php) ----
        itemsPage: 1,
        itemsPerPage: 10,

        get itemsTotalPages() {
            return Math.max(1, Math.ceil(this.items.length / this.itemsPerPage));
        },

        get itemsPageStart() {
            return (this.itemsPage - 1) * this.itemsPerPage;
        },

        get itemsPageEnd() {
            return Math.min(this.items.length, this.itemsPageStart + this.itemsPerPage);
        },

        goToItemsPage(page) {
            this.itemsPage = Math.min(Math.max(1, page), this.itemsTotalPages);
        },

        // ---- UI state ----
        advancedOpen: false,
        activeDrawer: null, // null | 'item' | 'import' | 'ai-items' | 'ai-translation' | 'preview'
        editingIndex: null,
        draft: null,
        itemTypes: ITEM_TYPES,

        importTab: 'paste',
        importType: 'kosakata',
        importText: '',
        importPreview: [],

        aiItemsForm: { topic: '', level: '', count: 10, types: { kana: true, kosakata: true, kanji: false, grammar: false }, language: 'Indonesia' },
        aiMessage: null,
        aiLoading: false,
        aiGeneratedItems: [],
        aiGeneratedTranslation: null,

        lastSavedAt: null,
        restorableDraft: null,
        draftStorageKey: draftKeyFor(draftKey),

        init() {
            this.$nextTick(() => this.mountSortable());

            const found = readDraft(this.draftStorageKey);

            if (justSaved) {
                clearDraft(draftKeyFor('new'));
                clearDraft(this.draftStorageKey);
            } else if (found) {
                this.restorableDraft = found;
            }

            this.$watch('title', (value) => {
                if (!this.slugTouched) this.slug = slugify(value);
            });

            // Removing the last item(s) on the last page shouldn't strand the
            // view on a now-empty page.
            this.$watch('items.length', () => {
                if (this.itemsPage > this.itemsTotalPages) this.itemsPage = this.itemsTotalPages;
            });

            const persistDraft = debounce(() => {
                writeDraft(this.draftStorageKey, this.snapshot());
                this.lastSavedAt = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }, 2000);

            this.$watch(() => JSON.stringify(this.snapshot()), persistDraft);
        },

        snapshot() {
            return {
                title: this.title,
                slug: this.slug,
                moduleId: this.moduleId,
                level: this.level,
                summary: this.summary,
                content: this.content,
                translatedContent: this.translatedContent,
                videoUrl: this.videoUrl,
                estimatedMinutes: this.estimatedMinutes,
                xpReward: this.xpReward,
                sortOrder: this.sortOrder,
                isPublished: this.isPublished,
                items: this.items,
            };
        },

        restoreDraft() {
            if (!this.restorableDraft) return;

            Object.assign(this, this.restorableDraft.state);
            this.slugTouched = true; // a restored slug should not silently get overwritten again
            this.restorableDraft = null;
        },

        dismissDraft() {
            this.restorableDraft = null;
        },

        markSlugTouched() {
            this.slugTouched = true;
        },

        // ---- content hints ----
        get contentWordCount() {
            return this.content.trim() ? this.content.trim().split(/\s+/).length : 0;
        },

        get contentParagraphCount() {
            return this.content.trim() ? this.content.trim().split(/\n\s*\n/).length : 0;
        },

        // ---- item drag & drop ----
        mountSortable() {
            const list = this.$refs.itemList;

            if (!list) return;

            Sortable.create(list, {
                animation: 160,
                handle: '[data-item-handle]',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: (event) => {
                    if (event.oldIndex === event.newIndex) return;

                    const moved = this.items.splice(event.oldIndex, 1)[0];
                    this.items.splice(event.newIndex, 0, moved);
                },
            });
        },

        // ---- item drawer ----
        templateFor,

        get currentDrawerFields() {
            return this.draft ? TEMPLATE_FIELDS[templateFor(this.draft.type)] ?? [] : [];
        },

        getField(target) {
            if (!this.draft) return '';

            return target.startsWith('extra.')
                ? this.draft.extra?.[target.slice(6)] ?? ''
                : this.draft[target] ?? '';
        },

        setField(target, value) {
            if (!this.draft) return;

            if (target.startsWith('extra.')) {
                this.draft.extra = { ...this.draft.extra, [target.slice(6)]: value };
            } else {
                this.draft[target] = value;
            }
        },

        openAddItem() {
            this.editingIndex = null;
            this.draft = blankItem(null, this.moduleContentType);
            this.activeDrawer = 'item';

            this.$nextTick(() => this.$refs.itemDrawer?.querySelector('input, textarea')?.focus());
        },

        openEditItem(index) {
            this.editingIndex = index;
            this.draft = structuredClone(this.items[index]);
            this.activeDrawer = 'item';

            this.$nextTick(() => this.$refs.itemDrawer?.querySelector('input, textarea')?.focus());
        },

        pickType(type) {
            if (!this.draft) return;

            this.draft.type = type;
        },

        saveDrawer() {
            if (!this.draft || !this.draft.term?.trim()) return;

            this.draft.extra = { ...this.draft.extra, type: this.draft.type };
            applyReadingMirror(this.draft);

            if (this.editingIndex === null) {
                this.items.push(this.draft);
                this.itemsPage = this.itemsTotalPages; // jump to the page holding the item just added
            } else {
                this.items[this.editingIndex] = this.draft;
            }

            this.closeDrawer();
        },

        duplicateItem(index) {
            const copy = { ...structuredClone(this.items[index]), id: null, _uid: uid() };
            this.items.splice(index + 1, 0, copy);
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        closeDrawer() {
            this.activeDrawer = null;
            this.editingIndex = null;
            this.draft = null;
        },

        // ---- bulk paste import ----
        get importRows() {
            return parsePastedText(this.importText);
        },

        openImport() {
            this.importTab = 'paste';
            this.importText = '';
            this.activeDrawer = 'import';
        },

        confirmImport() {
            const fields = TEMPLATE_FIELDS[this.importType] ?? [];

            this.importRows.forEach((columns) => {
                const item = blankItem(this.importType, this.moduleContentType);

                fields.forEach((field, i) => {
                    if (columns[i] === undefined) return;
                    this.setFieldOn(item, field.target, columns[i]);
                });

                if (item.term.trim()) this.items.push(item);
            });

            this.itemsPage = this.itemsTotalPages;
            this.closeDrawer();
        },

        setFieldOn(item, target, value) {
            if (target.startsWith('extra.')) {
                item.extra = { ...item.extra, [target.slice(6)]: value };
            } else {
                item[target] = value;
            }
        },

        // ---- client-side validation hints (non-blocking — server is the real gate) ----
        problems(item, index) {
            const issues = [];

            if (!item.term.trim()) {
                issues.push(`Item ke-${index + 1} belum punya istilah.`);
                return issues;
            }

            // What counts as "incomplete" depends on the item's type — kana
            // has no `meaning` field at all (romaji is its core content),
            // so checking meaning/example_meaning for it is a false positive.
            const type = templateFor(item.type);

            if (type === 'kana') {
                if (!item.romaji.trim()) issues.push(`Item ke-${index + 1} belum punya romaji.`);
            } else if (type === 'kanji') {
                if (!item.meaning.trim()) issues.push(`Item ke-${index + 1} belum punya arti.`);
            } else if (!item.meaning.trim() && !item.example_meaning.trim()) {
                issues.push(`Item ke-${index + 1} belum punya arti.`);
            }

            return issues;
        },

        get totalProblems() {
            return this.items.reduce((sum, item, index) => sum + this.problems(item, index).length, 0);
        },

        // ---- AI — see LessonController::generateItems/generateTranslation + LessonAiService ----
        async generateItems(url) {
            this.aiLoading = true;
            this.aiMessage = null;
            this.aiGeneratedItems = [];

            try {
                const response = await window.post(url, this.aiItemsForm);

                if (response.available && !response.error) {
                    this.aiGeneratedItems = (response.items ?? []).map((raw) => hydrateItem(raw, this.moduleContentType));
                    if (this.aiGeneratedItems.length === 0) this.aiMessage = 'AI tidak menghasilkan item apa pun. Coba ubah topik atau jenis.';
                } else {
                    this.aiMessage = response.message;
                }
            } catch {
                this.aiMessage = 'Terjadi kesalahan menghubungi layanan AI.';
            } finally {
                this.aiLoading = false;
            }
        },

        removeGeneratedItem(index) {
            this.aiGeneratedItems.splice(index, 1);
        },

        acceptGeneratedItems() {
            this.items.push(...this.aiGeneratedItems);
            this.aiGeneratedItems = [];
            this.itemsPage = this.itemsTotalPages;
            this.activeDrawer = null;
        },

        async generateTranslation(url) {
            this.aiLoading = true;
            this.aiMessage = null;
            this.aiGeneratedTranslation = null;

            try {
                const response = await window.post(url, { content: this.content, level: this.level });

                if (response.available && !response.error) {
                    this.aiGeneratedTranslation = response.translation ?? '';
                } else {
                    this.aiMessage = response.message;
                }
            } catch {
                this.aiMessage = 'Terjadi kesalahan menghubungi layanan AI.';
            } finally {
                this.aiLoading = false;
            }
        },

        useGeneratedTranslation() {
            this.translatedContent = this.aiGeneratedTranslation ?? '';
            this.aiGeneratedTranslation = null;
            this.activeDrawer = null;
        },

        // ---- publish/draft submit ----
        submitAs(published) {
            this.isPublished = published;

            this.$nextTick(() => this.$refs.form.requestSubmit());
        },
    };
}
