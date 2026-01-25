# Multi-Language Setup Documentation

This project has been configured with multi-language support using:
- **Spatie Laravel Translatable** - For database model translations
- **Spatie Laravel Sluggable** - For URL-friendly slugs
- **JSON Translation Files** - For frontend translations
- **Locale-aware routing** - Default locale has no prefix, others use `/{locale}/`

## Environment Configuration

Add these variables to your `.env` file:

```env
# Default locale (no prefix in URLs)
APP_LOCALE=en

# Supported locales (comma-separated)
APP_SUPPORTED_LOCALES=en,ru,tj

# Hide default locale in URL (true = /about, false = /en/about)
APP_HIDE_DEFAULT_LOCALE=true
```

## How It Works

### URL Structure

- **Default locale (en)**: `/` or `/about` (no prefix)
- **Other locales**: `/ru/` or `/ru/about` (with prefix)

### Translation Files

Translation files are stored in `lang/{locale}/common.json`:

- `lang/en/common.json` - English translations
- `lang/ru/common.json` - Russian translations  
- `lang/tj/common.json` - Tajik translations

### Using Translations in Vue Components

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const { __ } = usePage().props;
</script>

<template>
    <div>{{ __('common.home') }}</div>
</template>
```

Or use Laravel's `__()` helper in Blade templates:

```blade
{{ __('common.home') }}
```

## Language Switcher

The `LanguageSwitcher` component has been added to `GuestLayout.vue`. It automatically:
- Shows current locale
- Lists all supported locales
- Switches locale while staying on the same page

## Using Spatie Traits

### Translatable Models

Example model with translatable fields:

```php
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description', 'content'];
}
```

Usage:
```php
$post = Post::create([
    'title' => [
        'en' => 'English Title',
        'ru' => 'Русский Заголовок',
        'tj' => 'Сарлавҳаи Тоҷикӣ'
    ]
]);

$post->title; // Gets title in current locale
$post->getTranslation('title', 'ru'); // Gets Russian title
```

### Sluggable Models

Example model with slug generation:

```php
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
```

See `app/Models/ExampleModel.php` for a complete example.

## Routes

Routes are automatically registered for each locale. The default locale routes have no prefix:

```php
// Default locale (en)
Route::get('/', ...)->name('index');
Route::get('dashboard', ...)->name('dashboard');

// Other locales
Route::prefix('ru')->group(function() {
    Route::get('/', ...)->name('ru.index');
    Route::get('dashboard', ...)->name('ru.dashboard');
});
```

## Locale Helper

Use the `LocaleHelper` class for locale-related operations:

```php
use App\Helpers\LocaleHelper;

$currentLocale = LocaleHelper::getCurrentLocale();
$supportedLocales = LocaleHelper::getSupportedLocales();
$localeName = LocaleHelper::getLocaleName('ru'); // Returns "Русский"
$switchUrl = LocaleHelper::switchLocaleUrl('ru');
```

## Adding New Locales

1. Add locale to `APP_SUPPORTED_LOCALES` in `.env`
2. Create translation file: `lang/{locale}/common.json`
3. Add locale name to `config/locale.php`:
   ```php
   'locale_names' => [
       'en' => 'English',
       'ru' => 'Русский',
       'tj' => 'Тоҷикӣ',
       'new' => 'New Language', // Add here
   ],
   ```
4. Routes will automatically include the new locale

## Middleware

The `SetLocale` middleware automatically:
- Detects locale from URL segment
- Sets application locale
- Stores locale in session
- Handles default locale routing

## Example Usage

### In Controllers

```php
use Illuminate\Support\Facades\App;

// Get current locale
$locale = App::getLocale();

// Set locale
App::setLocale('ru');

// Translate
$title = __('common.home');
```

### In Vue Components

Locale data is automatically shared via Inertia:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const locale = page.props.locale; // Current locale
const supportedLocales = page.props.supportedLocales;
const localeNames = page.props.localeNames;
</script>
```
