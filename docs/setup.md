# Setup Instructions

This guide walks you through setting up the Local Development Environment for the platform.

## Prerequisites

Ensure you have the following installed on your machine:

- **PHP** >= 8.2
- **Composer**
- **Node.js** (v20+ recommended) & **npm**

---

## Installation Steps

1. **Clone the repository**

    ```bash
    git clone <repo-url> nbt-price-comparison
    cd nbt-price-comparison
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment Configuration**
   Copy the example environment variables file and configure your local settings:

    ```bash
    cp .env.example .env
    ```

    _Note: Ensure the default SQLite DB or your local DB credentials are correct._

5. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

6. **Database Migration and Seeding**
   Create the SQLite database file if required, then migrate the database schema and seed the initial data:

    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

7. **Link Storage**
   Needed for Spatie Media Library and uploaded assets (like avatars, logos, and post images).
    ```bash
    php artisan storage:link
    ```

---

## Running the Application

You can use the built-in Composer script which concurrently runs Vite, the Laravel Server, queue workers, and log streams in a single tab:

```bash
npm run dev
# OR using the composer wrapper:
composer dev
```

The application will be accessible at: `http://localhost:8000`

### Server-Side Rendering (SSR)

For SSR (typically used in production or for SEO debugging), use:

```bash
composer dev:ssr
```
