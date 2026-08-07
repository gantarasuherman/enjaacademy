/**
 * Role × Permission (or Role × Menu) matrix.
 *
 * Handles the bulk selection affordances a matrix needs — toggle a whole row,
 * a whole column, a module group, or everything — plus a live "changed" count
 * so an admin knows a save is pending.
 */
export default function permissionMatrix({ initial = {}, superRoleId = null } = {}) {
    return {
        // roleId -> Set of checked ids, kept as arrays for Alpine reactivity.
        selected: Object.fromEntries(Object.entries(initial).map(([role, ids]) => [role, [...ids]])),
        baseline: JSON.stringify(initial),
        search: '',

        isChecked(roleId, id) {
            return (this.selected[roleId] ?? []).includes(id);
        },

        toggle(roleId, id) {
            if (this.isLocked(roleId)) return;

            const current = this.selected[roleId] ?? [];

            this.selected[roleId] = current.includes(id)
                ? current.filter((value) => value !== id)
                : [...current, id];
        },

        /** The super role is granted everything by Gate::before, so it is read-only. */
        isLocked(roleId) {
            return superRoleId !== null && Number(roleId) === Number(superRoleId);
        },

        toggleRow(roleId, ids) {
            if (this.isLocked(roleId)) return;

            const current = this.selected[roleId] ?? [];
            const allSet = ids.every((id) => current.includes(id));

            this.selected[roleId] = allSet
                ? current.filter((id) => !ids.includes(id))
                : [...new Set([...current, ...ids])];
        },

        rowState(roleId, ids) {
            const current = this.selected[roleId] ?? [];
            const hits = ids.filter((id) => current.includes(id)).length;

            return hits === 0 ? 'none' : hits === ids.length ? 'all' : 'some';
        },

        toggleColumn(id, roleIds) {
            const targets = roleIds.filter((roleId) => !this.isLocked(roleId));
            const allSet = targets.every((roleId) => this.isChecked(roleId, id));

            targets.forEach((roleId) => {
                const current = this.selected[roleId] ?? [];

                this.selected[roleId] = allSet
                    ? current.filter((value) => value !== id)
                    : [...new Set([...current, id])];
            });
        },

        toggleAll(roleIds, ids) {
            const targets = roleIds.filter((roleId) => !this.isLocked(roleId));
            const allSet = targets.every((roleId) => ids.every((id) => this.isChecked(roleId, id)));

            targets.forEach((roleId) => {
                this.selected[roleId] = allSet ? [] : [...ids];
            });
        },

        get changed() {
            return JSON.stringify(this.selected) !== this.baseline;
        },

        get totalSelected() {
            return Object.values(this.selected).reduce((sum, ids) => sum + ids.length, 0);
        },

        /** Case-insensitive filter used to hide non-matching module groups. */
        matches(label) {
            if (!this.search.trim()) return true;

            return label.toLowerCase().includes(this.search.trim().toLowerCase());
        },
    };
}
