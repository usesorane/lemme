import Fuse from 'fuse.js';

class LemmeSearch {
    constructor() {
        this.fuse = null;
        this.data = [];
        this.initialized = false;

        // Fuse.js options
        this.options = {
            keys: [
                {
                    name: 'title',
                    weight: 0.7
                },
                {
                    name: 'content',
                    weight: 0.3
                },
                {
                    name: 'category',
                    weight: 0.2
                }
            ],
            threshold: 0.3, // Lower threshold means more exact matches
            includeScore: true,
            includeMatches: true,
            minMatchCharLength: 2,
            shouldSort: true,
            findAllMatches: false,
            location: 0,
            distance: 100,
            ignoreLocation: false,
            ignoreFieldNorm: false
        };
    }

    /**
     * Initialize Fuse.js with data
     * @param {Array} data - Array of search data
     */
    init(data) {
        this.data = data;
        this.fuse = new Fuse(data, this.options);
        this.initialized = true;
    }

    /**
     * Search using Fuse.js
     * @param {string} query - Search query
     * @param {number} limit - Maximum number of results
     * @returns {Array} - Search results
     */
    search(query, limit = 5) {
        if (!this.initialized || !query.trim()) {
            return [];
        }

        const results = this.fuse.search(query, { limit });

        return results.map(result => ({
            ...result.item,
            score: result.score,
            matches: result.matches
        }));
    }

    /**
     * Add new item to search index
     * @param {Object} item - Item to add
     */
    addItem(item) {
        this.data.push(item);
        if (this.initialized) {
            this.fuse.setCollection(this.data);
        }
    }

    /**
     * Update search data
     * @param {Array} data - New search data
     */
    updateData(data) {
        this.init(data);
    }

    /**
     * Get highlighted search terms
     * @param {string} text - Text to highlight
     * @param {Array} matches - Fuse.js matches
     * @param {string} field - Field name to match
     * @returns {string} - Highlighted text
     */
    highlightMatches(text, matches, field) {
        if (!matches || matches.length === 0) {
            return text;
        }

        const fieldMatches = matches.filter(match => match.key === field);
        if (fieldMatches.length === 0) {
            return text;
        }

        let highlightedText = text;
        const ranges = [];

        // Collect all match ranges
        fieldMatches.forEach(match => {
            if (match.indices) {
                match.indices.forEach(([start, end]) => {
                    ranges.push({ start, end });
                });
            }
        });

        // Sort ranges by start position (descending to replace from end to beginning)
        ranges.sort((a, b) => b.start - a.start);

        // Apply highlights
        ranges.forEach(({ start, end }) => {
            const before = highlightedText.substring(0, start);
            const match = highlightedText.substring(start, end + 1);
            const after = highlightedText.substring(end + 1);

            highlightedText = before +
                '<mark class="underline bg-transparent text-emerald-500">' +
                match +
                '</mark>' +
                after;
        });

        return highlightedText;
    }
}

// Make it globally available
window.LemmeSearch = LemmeSearch;

// Initialize for Livewire
document.addEventListener('DOMContentLoaded', function () {
    // Create global search instance
    window.lemmeSearchInstance = new LemmeSearch();

    // Listen for Livewire events to initialize search data
    document.addEventListener('livewire:initialized', function () {
        // Dispatch event to get initial search data
        Livewire.dispatch('init-search-data');
    });
});

export default LemmeSearch;
