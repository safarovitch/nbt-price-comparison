# NBT Price Comparison Platform

> **Централизованный сайт для сравнения цен/предложений** (Centralized website for price/offer comparison)

A comprehensive financial comparison platform built with **Laravel 12**, **Inertia.js**, and **Vue 3**. It allows users to compare various financial products such as credits, insurance, exchange rates, and view bank ratings, along with tools like loan and deposit calculators.

## 🚀 Tech Stack

### Backend

- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: SQLite (default configuration, easily switchable to MySQL/PostgreSQL)
- **Key Packages**:
    - `inertiajs/inertia-laravel` for SPA routing & bridging
    - `laravel/fortify` for authentication
    - `spatie/laravel-permission` for roles & permissions
    - `spatie/laravel-translatable` for model translations
    - `spatie/laravel-medialibrary` for handling media uploads

### Frontend

- **Framework**: Vue 3 (Composition API / `<script setup>`)
- **Routing**: Inertia.js
- **Build Tool**: Vite
- **Styling**: Bootstrap 5
- **Language**: TypeScript & JavaScript
- **Key Packages**:
    - `@inertiajs/vue3`
    - `bootstrap` (UI Components)
    - `@amcharts/amcharts5` (Charts and Visualizations)
    - `leaflet` (Map features for ATMs/Branches)
    - `@editorjs/editorjs` (Rich text editing)

---

## 📂 Directory Structure

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
│   │   ├── Pages/          # Vue/Inertia Page Components (Admin, Credits, Exchange, etc.)
│   │   ├── Components/     # Reusable Vue Components
│   │   └── Layouts/        # Global Layout Wrappers (AdminLayout, AppLayout, etc.)
│   └── css/                # Global Stylesheets
├── public/                 # Compiled assets and uploads
└── config/                 # Laravel configuration files
```

---

## ✨ Features Breakdown

1. **Localization & RTL**: Full multilingual support (`en`, `ru`, `tg`) built precisely into the routing, models, and UI. Handled via the custom `LocaleHelper`.
2. **Financial Products**: Catalog for checking and comparing different typed products (`credits`, `insurance`, `deposits`).
3. **Calculators**: Integrated Loan, Credit, and Deposit calculators implemented entirely in Vue and utilizing API endpoints.
4. **Exchange Rates API**: A dynamic currency exchange module that retrieves and computes exchange data.
5. **ATMs & Branches Locator**: `DeviceLocation` maps using Leaflet.js to pinpoint bank branches globally.
6. **Ratings & Reviews**: System for clients to submit application ratings.
7. **Role-Based Admin Panel**: Secure backend system integrated via Spatie Permissions to manage organizations, products, and incoming applications.

---

## 🛠️ Setup Instructions

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js (v20+ recommended) & npm

### Installation Steps

1. **Clone the repository** (if you haven't already):

    ```bash
    git clone <repo-url> nbt-price-comparison
    cd nbt-price-comparison
    ```

2. **Install PHP dependencies**:

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**:

    ```bash
    npm install
    ```

4. **Environment Configuration**:
   Copy `.env.example` to `.env`:

    ```bash
    cp .env.example .env
    ```

    _Note: Ensure the default SQLite DB or your local DB credentials are correct._

5. **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

6. **Database Migration and Seeding**:
   Create the SQLite database file if required, then migrate the database schema:

    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

7. **Link Storage**:
   Needed for Spatie Media Library and uploaded assets.
    ```bash
    php artisan storage:link
    ```

### 🏃‍♂️ Running the Application

You can use the built-in Composer script which concurrently runs Vite, Laravel Server, queues, and logs:

```bash
npm run dev
# OR using the composer wrapper:
composer dev
```

The application will be accessible at: `http://localhost:8000`

For **Server Side Rendering (SSR)** (used in production or for SEO purposes), use:

```bash
composer dev:ssr
```

---

## 🧩 Architectural Concepts

### Routing & Locales

All client-facing routes are wrapped inside a locale matcher middleware in `routes/web.php`.
Example: `/{locale}/credits` routes to the `CreditsController` while respecting current translation settings. Valid locales are `en`, `ru`, and `tg`.

### Client-Side State & SSR

State is injected from Laravel Controllers into Vue Pages as props via Inertia (`Inertia::render()`).

### External Tools

- Code formatting and validation enforced via **ESLint** and **Prettier** (`npm run lint`, `npm run format`).
- Types are strictly checked using the extensive `tsconfig.json`.

---

_Generated based on current codebase context._
