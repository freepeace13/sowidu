# Translation

A Laravel package for database-driven translation management with file and database loaders.

## Installation

First, ensure that the package is located in `./packages/translation` and registered in your `composer.json` repositories:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/*",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

Then, install it using composer:

```bash
composer require sowidu/translation:1.x-dev
```

## Setup

Publish the configuration and migrations:

```bash
php artisan vendor:publish --provider="Packages\Translation\TranslationServiceProvider" --tag=config
php artisan vendor:publish --provider="Packages\Translation\TranslationServiceProvider" --tag=migrations
```

Run the migrations:

```bash
php artisan migrate
```

Configure your locales in `config/translation.php`:

```php
return [
    'model' => Packages\Translation\LanguageLine::class,
    'session_key' => 'translation.locale',
    'locales' => [
        'en' => 'English',
        'de' => 'German/Deutsch',
    ],
    'loaders' => [
        Packages\Translation\Loaders\DatabaseLoader::class,
    ],
];
```

## Usage

### Translation Manager

```php
use Packages\Translation\TranslationManager;

$manager = app('sowidu.translation');

// Get all translations
$all = $manager->driver('db')->all();

// Get translations for a group
$lines = $manager->driver('db')->lines('validation');

// Get specific translation line
$line = $manager->driver('db')->line('validation', 'required');

// Get translation for specific locale
$value = $manager->driver('db')->lineOf('validation', 'required', 'en');

// Check if translation exists
$exists = $manager->driver('db')->exists('validation', 'required');
```

### Language Line Model

```php
use Packages\Translation\LanguageLine;

// Create translation
$languageLine = LanguageLine::create([
    'group' => 'validation',
    'key' => 'custom.required',
    'text' => [
        'en' => 'This field is required',
        'de' => 'Dieses Feld ist erforderlich',
    ],
]);

// Get translation for specific locale
$translation = $languageLine->getTranslation('en');

// Set translation for locale
$languageLine->setTranslation('fr', 'Ce champ est obligatoire');
$languageLine->save();

// Get translations for group
$translations = LanguageLine::getTranslationsForGroup('en', 'validation');
```

### Facade

```php
use Packages\Translation\Facades\Translation;

$all = Translation::driver('db')->all();
$lines = Translation::driver('db')->lines('validation');
```

### Setting Preferred Locale

```php
use Packages\Translation\Actions\SetPreferredLocale;

// In a controller
$action = new SetPreferredLocale($request);
$locale = $action->handle();
```

### Middleware

Add the localization middleware to your routes:

```php
use Packages\Translation\Middleware\LocalizationMiddleware;

Route::middleware([LocalizationMiddleware::class])->group(function () {
    // Your routes
});
```

Or add it globally in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Packages\Translation\Middleware\LocalizationMiddleware::class,
    ],
];
```

## Loaders

The package supports multiple loaders:

- **FileLoader** - Loads translations from language files
- **DatabaseLoader** - Loads translations from database

Loaders are configured in `config/translation.php`:

```php
'loaders' => [
    Packages\Translation\Loaders\DatabaseLoader::class,
],
```

## Service Provider

The service provider is auto-discovered. If needed, you can manually register it:

```php
'providers' => [
    Packages\Translation\TranslationServiceProvider::class,
],
```

## Architecture

The package uses a repository pattern with readers:

- `Packages\Translation\TranslationRepository` - Main repository
- `Packages\Translation\Readers\ReaderInterface` - Reader interface
- `Packages\Translation\Readers\FileReader` - File-based reader
- `Packages\Translation\Readers\DatabaseReader` - Database-based reader
- `Packages\Translation\LoaderManager` - Manages translation loaders
- `Packages\Translation\LoaderInterface` - Loader interface
- `Packages\Translation\Loaders\DatabaseLoader` - Database loader implementation
