# Architecture Overview

The system architecture blends a monolithic Laravel server with a reactive Vue SPA, bridged natively by Inertia.js.

## Tech Stack

### Backend

- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: SQLite (default configuration, easily switchable to MySQL/PostgreSQL)
- **Key Packages**:
    - `inertiajs/inertia-laravel` for SPA routing & bridging
    - `laravel/fortify` for deep authentication logic
    - `spatie/laravel-permission` for roles & permissions
    - `spatie/laravel-translatable` for JSON-based model translations
    - `spatie/laravel-medialibrary` for handling media uploads effortlessly

### Frontend

- **Framework**: Vue 3 (Composition API / `<script setup>`)
- **Routing**: Inertia.js (controlled by server routes)
- **Build Tool**: Vite
- **Styling**: Bootstrap 5 + custom CSS/SCSS
- **Language**: TypeScript & JavaScript
- **Key Packages**:
    - `bootstrap` (UI Components)
    - `@amcharts/amcharts5` (Charts and Visualizations)
    - `leaflet` (Map features for ATMs/Branches)
    - `@editorjs/editorjs` (Rich text content editing for News/Blog)

---

## Directory Structure

A brief overview of the essential directories mapped to our architecture:

```text
nbt-price-comparison/
├── app/
│   ├── Http/Controllers/   # Backend endpoints (API & Web)
│   ├── Models/             # Eloquent Models (User, Product, Organization, etc.)
│   └── Helpers/            # Custom helpers (e.g., LocaleHelper)
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/            # Initial database population logic
├── routes/
│   ├── web.php             # Main web & localized routes
│   └── admin.php           # Admin panel routes
├── resources/
│   ├── js/
│   │   ├── Pages/          # Vue/Inertia Page Components
│   │   ├── Components/     # Reusable Vue Components
│   │   └── Layouts/        # Global Layout Wrappers (AdminLayout, AppLayout)
│   └── css/                # Global Stylesheets
├── public/                 # Compiled assets and uploads
└── docs/                   # Developer documentation files (MkDocs)
```
