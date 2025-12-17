# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Lovely London by Carol** is a tourism website for a certified Brazilian tour guide in London. This is a PHP-based multilingual (Portuguese/English) website with a custom JSON-based database system instead of MySQL.

## Architecture

### Database System

**CRITICAL**: This project uses a custom JSON database (`JSONDatabase`) instead of MySQL/PDO.

- Location: `includes/json_database.php`
- Data files: `data/*.json` (e.g., `tours.json`, `blog_posts.json`, `testimonials.json`)
- The `JSONDatabase` class simulates PDO interface for compatibility
- Connection initialized in: `includes/pdo_connection.php` (creates `$pdo` global)
- All CRUD operations work through this JSON system

**Available Tables**:
- `users`, `admin_users`
- `tours`, `services`, `blog_posts`, `blog_categories`
- `testimonials`, `faqs`, `hero_slides`, `hero_slide_items`
- `features`, `gallery_photos`, `clients`
- `page_content`, `page_sections`, `page_section_items`
- `site_settings`, `seo_metadata`, `section_backgrounds`
- `contact_submissions`, `bookings`, `virtual_tour_locations`

### Directory Structure

```
v2/
├── index.php              # Main homepage
├── pages/                 # Public pages (tours.php, blog.php, services.php, etc.)
├── admin/                 # Admin panel
│   ├── config.php         # Admin configuration (JSONDatabase init)
│   ├── modules/           # Admin CRUD modules for each content type
│   └── handlers/          # Background/upload handlers
├── includes/              # Core PHP includes
│   ├── json_database.php  # Custom JSON database class
│   ├── content_helpers.php # Data retrieval functions
│   ├── header.php         # Site header with SEO meta
│   ├── footer.php         # Site footer
│   └── lang.php           # Multilingual support
├── data/                  # JSON database files
├── assets/                # Static assets (images, uploads)
├── css/                   # Stylesheets
├── js/                    # JavaScript files
├── mobile/                # Mobile app-style interface
└── migrations/            # SQL migrations (legacy, not used)
```

### Multilingual System

- Languages: Portuguese (default) and English
- Implementation: `includes/lang.php`
- Language detection: Query param `?lang=en` or session storage
- Content fields: All database records have `field_pt` and `field_en` columns
- Helper function: `getContent($item, 'field')` returns content in current language

### Content Helper Functions

Located in `includes/content_helpers.php`:

- `getTours($limit, $featured_only)` - Fetch active tours
- `getServices($limit)` - Fetch active services (JSON first, then DB)
- `getBlogPosts($limit, $featured_only)` - Fetch published blog posts
- `getTestimonials($limit, $featured_only)` - Fetch approved testimonials
- `getHeroSlides()` - Fetch hero slides with items
- `getClients($limit)` - Fetch active client logos
- `getFAQs($limit)` - Fetch active FAQs
- `getContent($item, $field)` - Get multilingual content
- `formatHeroText($text)` - Format hero text with color tags like `[skyline]London[/skyline]`

### Admin Panel

- Location: `admin/index.php`
- Auth: Session-based (`requireAuth()` in `admin/config.php`)
- Modules: Each content type has a module in `admin/modules/`
- Database: Uses global `$db` (JSONDatabase instance)
- Constants: Colors, paths, upload categories defined in `admin/config.php`

### Base Path Detection

The site supports multiple deployment folders (`/v2/`, `/stg/`, root). Base path is auto-detected in `includes/header.php`:

```php
if (strpos($request_uri, '/v2/') !== false) {
    $base_path = '/v2';
} elseif (strpos($request_uri, '/stg/') !== false) {
    $base_path = '/stg';
} else {
    $base_path = '';
}
```

Always use `$base_path` for internal links and asset paths.

## Development Commands

### Testing
No automated test suite exists. Manual testing required through browser.

### Viewing the Site
- Main site: Open `index.php` in browser at configured base path
- Admin panel: Navigate to `admin/login.php`
- Mobile version: Navigate to `mobile/index.php`

### Working with Data

**Adding/Editing Content**: Use admin panel at `admin/index.php` (requires login)

**Direct JSON Editing**: Edit files in `data/` directory
- Structure: Array of objects with `id`, `created_at`, `updated_at` timestamps
- Always include both `field_pt` and `field_en` for content fields
- Maintain consistent ID generation (integer auto-increment)

### File Uploads

- Handler: `admin/upload_handler.php`
- Categories: tours, services, blog, hero, testimonials, gallery, clients, backgrounds
- Location: `assets/uploads/{category}/`
- Max size: 5MB
- Allowed: jpg, jpeg, png, webp, gif

## Key Patterns

### Database Queries

```php
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM tours WHERE is_active = 1");
$stmt->execute();
$tours = $stmt->fetchAll();
```

### Multilingual Content

```php
// In database: title_pt, title_en
echo getContent($tour, 'title'); // Returns based on current $lang
```

### Admin CRUD Pattern

All admin modules follow this pattern:
1. Check operation: `$_GET['op']` (list, add, edit, delete)
2. For list: Query and display table
3. For add/edit: Show form, process POST
4. For delete: Confirm and execute

### Color Palette

Brand colors defined in `admin/config.php`:
- `lovely`: #700420 (burgundy)
- `blackfriars`: #292828 (dark gray)
- `notting-hill`: #955425 (brown)
- `skyline`: #DAB59A (beige)
- `thames`: #7FA1C3 (blue)
- `fog-white`: #F8F9FA (off-white)

## Important Notes

- **No MySQL**: Despite PDO-like syntax, everything is JSON-based
- **Auto-increment IDs**: JSONDatabase handles ID generation automatically
- **Graceful degradation**: Site works without database connection (empty states)
- **Session auth**: Admin uses PHP sessions (`$_SESSION['logged_in']`)
- **SEO**: Each page has custom meta tags in `includes/header.php` config
- **Mobile version**: Separate mobile-optimized interface in `mobile/`
