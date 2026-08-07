import Sortable from 'sortablejs';

/**
 * Drag & drop menu tree.
 *
 * Every <ul data-menu-list> becomes a Sortable list sharing one group, so a
 * node can be dragged across nesting levels. On drop the whole tree is
 * serialised from the DOM and POSTed — the server is the only thing that
 * decides the final ordering.
 */
export default function menuTree({ reorderUrl, maxDepth = 5 }) {
    return {
        dirty: false,
        saving: false,
        instances: [],

        init() {
            this.mount();

            // Re-mount after Alpine re-renders any part of the tree.
            this.$watch('dirty', () => undefined);
        },

        mount() {
            this.instances.forEach((instance) => instance.destroy());
            this.instances = [];

            this.$el.querySelectorAll('[data-menu-list]').forEach((list) => {
                this.instances.push(
                    Sortable.create(list, {
                        group: 'menus',
                        animation: 160,
                        fallbackOnBody: true,
                        swapThreshold: 0.6,
                        handle: '[data-menu-handle]',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',

                        // Block drops that would exceed the configured nesting depth.
                        onMove: (event) => {
                            const targetDepth = Number(event.to.dataset.depth ?? 0);
                            const draggedChildren = event.dragged.querySelectorAll('[data-menu-list]').length;

                            return targetDepth + draggedChildren < maxDepth;
                        },

                        onEnd: () => {
                            this.dirty = true;
                        },
                    }),
                );
            });
        },

        /** Walks the DOM and rebuilds the nested id structure. */
        serialise(list) {
            return Array.from(list.children)
                .filter((node) => node.dataset.menuId)
                .map((node) => {
                    const childList = node.querySelector('[data-menu-list]');

                    return {
                        id: Number(node.dataset.menuId),
                        children: childList ? this.serialise(childList) : [],
                    };
                });
        },

        async save() {
            const root = this.$el.querySelector('[data-menu-list][data-depth="0"]');

            if (!root) return;

            this.saving = true;

            try {
                const { message } = await window.post(reorderUrl, { tree: this.serialise(root) });

                this.dirty = false;
                Alpine.store('toasts').push(message ?? 'Menu order saved.', 'success');
            } catch (error) {
                Alpine.store('toasts').push(
                    error?.response?.data?.message ?? 'Failed to save the new order.',
                    'error',
                );
            } finally {
                this.saving = false;
            }
        },

        reset() {
            window.location.reload();
        },
    };
}
