/**
 * Icon picker for the menu form.
 *
 * The list mirrors the registry in `resources/views/components/icon.blade.php`
 * — anything chosen here is guaranteed to render. Emoji and single characters
 * are also accepted, which is how the Japanese modules get あ / ア / 漢.
 */
const ICONS = [
    'home', 'gauge', 'grid', 'database', 'layers', 'book-open', 'book', 'file-text',
    'clipboard', 'shield', 'users', 'user-check', 'key', 'table', 'menu', 'list',
    'plus', 'chart', 'trending-up', 'target', 'trophy', 'medal', 'activity',
    'settings', 'sliders', 'history', 'archive', 'help', 'language', 'bookmark',
    'note', 'cards', 'question', 'flag-jp', 'flag-en', 'certificate', 'sitemap',
    'chat', 'headphone', 'microphone', 'newspaper', 'pencil',
];

const EMOJI = ['あ', 'ア', '漢', '🇯🇵', '🇬🇧', '📚', '🎯', '🔥', '⭐', '🏆', '✍️', '🎧', '🗣️', '📝'];

export default function iconPicker({ initial = '' } = {}) {
    return {
        open: false,
        value: initial,
        search: '',
        icons: ICONS,
        emoji: EMOJI,

        get filtered() {
            const term = this.search.trim().toLowerCase();

            return term ? this.icons.filter((icon) => icon.includes(term)) : this.icons;
        },

        select(icon) {
            this.value = icon;
            this.open = false;
            this.search = '';
        },

        clear() {
            this.value = '';
        },
    };
}
