/**
 * Quiz question builder.
 *
 * Posts `questions[n][...]` including nested `options[m][...]`, matching what
 * QuizRequest validates and QuizService::syncQuestions writes.
 */
function blankOption(label = '') {
    return { label, is_correct: false };
}

function blankQuestion() {
    return {
        id: null,
        question: '',
        type: 'multiple_choice',
        explanation: '',
        correct_text: '',
        score: 1,
        options: [blankOption(), blankOption(), blankOption(), blankOption()],
    };
}

export default function questionBuilder({ initial = [] } = {}) {
    return {
        questions: initial.length
            ? initial.map((question) => ({
                  ...question,
                  options: (question.options ?? []).map((option) => ({ ...option })),
              }))
            : [blankQuestion()],

        add() {
            this.questions.push(blankQuestion());

            this.$nextTick(() => {
                const fields = this.$el.querySelectorAll('[data-question-text]');
                fields[fields.length - 1]?.focus();
            });
        },

        remove(index) {
            this.questions.splice(index, 1);

            if (this.questions.length === 0) this.add();
        },

        move(index, delta) {
            const target = index + delta;

            if (target < 0 || target >= this.questions.length) return;

            [this.questions[index], this.questions[target]] = [this.questions[target], this.questions[index]];
        },

        /** True/false has a fixed pair of options; fill-blank has none at all. */
        onTypeChange(question) {
            if (question.type === 'true_false') {
                question.options = [blankOption('Benar'), blankOption('Salah')];
                return;
            }

            if (question.type === 'fill_blank') {
                question.options = [];
                return;
            }

            if (question.options.length < 2) {
                question.options = [blankOption(), blankOption(), blankOption(), blankOption()];
            }
        },

        addOption(question) {
            question.options.push(blankOption());
        },

        removeOption(question, index) {
            question.options.splice(index, 1);
        },

        /** "arrange" has no is_correct flag — row order IS the answer key, so it needs reordering instead. */
        moveOption(question, index, delta) {
            const target = index + delta;

            if (target < 0 || target >= question.options.length) return;

            [question.options[index], question.options[target]] = [question.options[target], question.options[index]];
        },

        /** Only one option may be correct, so selecting one clears the rest. */
        markCorrect(question, index) {
            question.options.forEach((option, i) => {
                option.is_correct = i === index;
            });
        },

        /** Mirrors the server-side validation so problems surface before saving. */
        problems(question) {
            const issues = [];

            if (!question.question.trim()) issues.push('Pertanyaan masih kosong.');

            if (question.type === 'fill_blank') {
                if (!question.correct_text.trim()) issues.push('Kunci jawaban belum diisi.');
                return issues;
            }

            const filled = question.options.filter((option) => option.label.trim() !== '');

            if (question.type === 'arrange') {
                if (filled.length < 2) issues.push('Butuh minimal dua kata untuk disusun.');
                return issues;
            }

            if (filled.length < 2) issues.push('Butuh minimal dua pilihan.');
            if (!filled.some((option) => option.is_correct)) issues.push('Tandai satu jawaban benar.');

            return issues;
        },

        get totalProblems() {
            return this.questions.reduce((sum, question) => sum + this.problems(question).length, 0);
        },
    };
}
