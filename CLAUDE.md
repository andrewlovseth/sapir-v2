# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

```bash
# Install dependencies
npm install

# Compile SCSS to CSS (one-time build)
gulp style

# Watch mode with BrowserSync (proxies https://sapirjournal-v2.local)
gulp watch
```

The gulpfile compiles `scss/style.scss` to `style.css` with autoprefixer and sourcemaps. Watch mode auto-reloads on changes to PHP files in root, `/templates/`, `/template-parts/`, `/blocks/`, and JS in `/js/`.

## Architecture

### Content Model

This is a publishing theme for Sapir Journal with these ACF-registered custom post types:
- **post** (Articles) - Main editorial content
- **issue** - Journal volumes that organize articles
- **authors** - Author profiles (separate from WP users)
- **conversations** - Interview/dialogue content
- **letters** - Reader correspondence
- **news** - News items
- **salons** - Event content

ACF field groups are version-controlled in `/acf-json/`.

### Template Structure

WordPress template hierarchy with partials:

```
single-{post-type}.php     → Entry points that compose template parts
templates/{page}/          → Page-specific template parts (e.g., home/latest.php)
template-parts/
  ├── global/              → Reusable components (teasers, pagination, forms)
  │   └── teaser/          → Article card variants (small, large, search-result)
  ├── header/              → Header components
  └── footer/              → Footer components (epigraph, share-modal)
```

Home page (`templates/home.php`) assembles sections: latest, featured, explore, curated-articles, quote.

### Functions Organization

`functions.php` requires modular files from `/functions/`:
- `theme-support.php` - WP features, custom labels ("Articles" instead of "Posts")
- `acf.php` - ACF field query customizations
- `sapir.php` - Auto-wraps "SAPIR" and "A.M./P.M." in small-caps spans
- `enqueue-styles-scripts.php` - Asset loading
- `register-blocks.php` - Gutenberg block registration
- `disable-gutenberg-editor.php` - Classic editor enforcement
- `performance-optimizations.php` - Frontend optimizations

### SCSS Organization

```
scss/
  ├── variables/     → Colors (issue-specific palettes), typography, fonts
  ├── mixins/        → Reusable SCSS mixins
  ├── typography/    → Type scales and text styles
  ├── layout/        → Grid and spacing
  ├── elements/      → Base HTML elements
  ├── header/        → Site header styles
  ├── footer/        → Site footer styles
  ├── blocks/        → Gutenberg block styles
  ├── templates/     → Page-specific styles
  ├── theme/         → Global theme styles
  └── print/         → Print stylesheet
```

Color palette includes issue-specific colors (e.g., `$issue-one-light-blue`, `$issue-six-orange`).

### JavaScript

`/js/site.js` handles:
- Mobile navigation toggle
- Search overlay
- Author A-Z tabs
- Newsletter modal (triggered by `?newsletter=subscribe`)
- Share modal with clipboard API
- Dropcap first-letter styling

Uses jQuery (WordPress bundled).

## Key Patterns

### Author Display
Authors are a separate CPT linked via ACF relationship fields, not WordPress users. SearchWP filter in `theme-support.php` indexes author content with articles.

### Text Transformations
`functions/sapir.php` auto-applies small-caps to "SAPIR" and "A.M./P.M." in titles, content, excerpts, and select ACF fields. Skips text already in `.small-caps` spans.

### SVG Icons
Located in `/svg/` as PHP files for inline rendering (e.g., `icon-fb.php`, `icon-search.php`).
