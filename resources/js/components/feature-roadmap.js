/**
 * Search, filter, and detail-panel state for the "Peta Fitur" tab.
 *
 * Filtering happens client-side over data already in the DOM (features come
 * from FeatureRoadmap::groups(), rendered server-side) — the same approach
 * as the role×permission matrix, just keyed on role/status instead of a
 * single search string.
 */
export default function featureRoadmap({ features = [] } = {}) {
    return {
        search: '',
        roleFilter: 'all',
        statusFilter: 'all',
        selectedId: null,
        features,

        get selected() {
            return this.features.find((feature) => feature.id === this.selectedId) ?? null;
        },

        open(id) {
            this.selectedId = id;
        },

        close() {
            this.selectedId = null;
        },

        byId(id) {
            return this.features.find((feature) => feature.id === id) ?? null;
        },

        /** Case-insensitive match against name + description. */
        matchesSearch(feature) {
            if (!this.search.trim()) return true;

            const needle = this.search.trim().toLowerCase();

            return feature.name.toLowerCase().includes(needle)
                || feature.description.toLowerCase().includes(needle);
        },

        matchesRole(feature) {
            return this.roleFilter === 'all' || feature.roles.includes(this.roleFilter);
        },

        matchesStatus(feature) {
            if (this.statusFilter === 'all') return true;
            if (feature.hasAccess === null) return this.statusFilter === 'unknown';

            return this.statusFilter === 'available' ? feature.hasAccess : !feature.hasAccess;
        },

        matches(feature) {
            return this.matchesSearch(feature) && this.matchesRole(feature) && this.matchesStatus(feature);
        },
    };
}
