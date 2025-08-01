# Fuse.js Search Implementation

This project now includes **Fuse.js v7.1.0** for enhanced fuzzy search functionality in the documentation.

## Features

- **Fuzzy Search**: Uses Fuse.js for intelligent text matching with typo tolerance
- **Weighted Search Fields**: 
  - Title: 70% weight (highest priority)
  - Content: 30% weight 
  - Category: 20% weight
- **Match Highlighting**: Highlights matched text in search results
- **Search Scoring**: Shows match confidence percentage
- **Real-time Search**: 300ms debounce for optimal performance

## How It Works

1. **JavaScript Integration**: The `src/search.js` file contains the Fuse.js implementation
2. **Livewire Component**: `SearchComponent.php` handles server-side logic and events
3. **Frontend Integration**: Alpine.js bindings connect the search UI with Fuse.js
4. **Asset Building**: Vite builds and bundles the search functionality

## Search Configuration

The current Fuse.js configuration in `src/search.js`:

```javascript
this.options = {
    keys: [
        { name: 'title', weight: 0.7 },
        { name: 'content', weight: 0.3 },
        { name: 'category', weight: 0.2 }
    ],
    threshold: 0.3,        // Lower = more exact matches
    includeScore: true,    // Include match scores
    includeMatches: true,  // Include match positions for highlighting
    minMatchCharLength: 2, // Minimum characters to match
    shouldSort: true,      // Sort by relevance
};
```

## Extending for Real Documentation

To integrate with actual documentation files instead of dummy data:

### 1. Update SearchComponent.php

Replace the `$dummyData` with actual documentation parsing:

```php
private function getDocumentationData()
{
    // Example: Parse markdown files from stubs directory
    $docs = [];
    $files = File::allFiles(base_path('stubs'));
    
    foreach ($files as $file) {
        if ($file->getExtension() === 'md') {
            $content = File::get($file->getPathname());
            $docs[] = [
                'title' => $this->extractTitle($content),
                'category' => $this->extractCategory($file->getPath()),
                'url' => $this->generateUrl($file),
                'content' => strip_tags($this->parseMarkdown($content)),
            ];
        }
    }
    
    return $docs;
}
```

### 2. Index Content at Build Time

For better performance, you could generate a search index file:

```php
// In a command or during build process
$searchIndex = $this->generateSearchIndex();
File::put(public_path('search-index.json'), json_encode($searchIndex));
```

### 3. Load Index in JavaScript

```javascript
// In search.js
async loadSearchIndex() {
    const response = await fetch('/search-index.json');
    const data = await response.json();
    this.init(data);
}
```

## Usage

The search is automatically initialized when the page loads. Users can:

- Press `Cmd+K` (Mac) or `Ctrl+K` (Windows/Linux) to open search
- Type to search with fuzzy matching
- See highlighted matches and relevance scores
- Click results to navigate

## Search Quality

The current implementation provides:
- **Typo tolerance**: "instalation" will match "installation"
- **Partial matching**: "auth" will match "authentication"
- **Relevance scoring**: Better matches appear first
- **Multi-field search**: Searches across title, content, and category
- **Visual feedback**: Highlighted matches and confidence scores
