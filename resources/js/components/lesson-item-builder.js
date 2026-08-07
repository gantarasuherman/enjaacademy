/**
 * Inline editor for a lesson's items (kana, kanji, vocabulary, grammar points).
 *
 * Rows are posted as `items[n][field]`, which is exactly the shape
 * LessonRequest validates and LearningService::syncItems consumes.
 */
export default function lessonItemBuilder({ initial = [] } = {}) {
    return {
        items: initial.length
            ? initial.map((item) => ({ ...item }))
            : [{ id: null, term: '', reading: '', romaji: '', meaning: '', example: '', example_meaning: '' }],

        add() {
            this.items.push({
                id: null,
                term: '',
                reading: '',
                romaji: '',
                meaning: '',
                example: '',
                example_meaning: '',
            });

            // Focus the new row's first field so typing can continue uninterrupted.
            this.$nextTick(() => {
                const inputs = this.$el.querySelectorAll('[data-item-term]');
                inputs[inputs.length - 1]?.focus();
            });
        },

        remove(index) {
            this.items.splice(index, 1);

            if (this.items.length === 0) this.add();
        },

        duplicate(index) {
            const copy = { ...this.items[index], id: null };
            this.items.splice(index + 1, 0, copy);
        },

        move(index, delta) {
            const target = index + delta;

            if (target < 0 || target >= this.items.length) return;

            [this.items[index], this.items[target]] = [this.items[target], this.items[index]];
        },

        get filled() {
            return this.items.filter((item) => item.term.trim() !== '').length;
        },
    };
}
