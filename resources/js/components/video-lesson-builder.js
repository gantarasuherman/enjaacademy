function uid() {
    return typeof crypto !== 'undefined' && crypto.randomUUID
        ? crypto.randomUUID()
        : `${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

function blankChapter() {
    return { _uid: uid(), id: null, term: '', timestamp: '' };
}

/**
 * Decluttered counterpart to `lessonBuilder()` for `content_type=video`
 * lessons — no rich-text editor, no polymorphic item-type drawer. Chapters
 * are a flat ordered list (title + `mm:ss`); array order on submit IS the
 * `sort_order` `LearningService::syncItems()` assigns server-side.
 */
export default function videoLessonBuilder({ lesson = {}, chapters = [] }) {
    return {
        title: lesson.title ?? '',
        slug: lesson.slug ?? '',
        moduleId: lesson.learning_module_id ?? '',
        level: lesson.level ?? '',
        summary: lesson.summary ?? '',
        videoUrl: lesson.video_url ?? '',
        estimatedMinutes: lesson.estimated_minutes ?? 10,
        xpReward: lesson.xp_reward ?? 20,
        sortOrder: lesson.sort_order ?? 0,
        isPublished: Boolean(lesson.is_published),

        chapters: chapters.map((c) => ({
            _uid: uid(),
            id: c.id ?? null,
            term: c.term ?? '',
            timestamp: c.extra?.timestamp ?? '',
        })),

        addChapter() {
            this.chapters.push(blankChapter());
        },

        removeChapter(index) {
            this.chapters.splice(index, 1);
        },

        moveChapter(index, direction) {
            const target = index + direction;

            if (target < 0 || target >= this.chapters.length) return;

            [this.chapters[index], this.chapters[target]] = [this.chapters[target], this.chapters[index]];
        },

        submitAs(published) {
            this.isPublished = published;

            this.$nextTick(() => this.$refs.form.requestSubmit());
        },
    };
}
