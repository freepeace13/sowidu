# Active Status

A Laravel package for managing active status on Eloquent models.

## Installation

First, ensure that the package is located in `./packages/active-status` and registered in your `composer.json` repositories:

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
composer require sowidu/active-status:1.x-dev
```

## Setup

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Packages\ActiveStatus\ActiveStatusServiceProvider" --tag=config
```

Create the migration:

```bash
php artisan active-status:create-migration YourModel
```

Create the middleware:

```bash
php artisan active-status:create-middleware YourModel
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

Add the `HasActiveStatus` trait to your model:

```php
use Packages\ActiveStatus\HasActiveStatus;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasActiveStatus;
    
    // ...
}
```

### Check Status

```php
$user = User::find(1);

if ($user->isStatus('active')) {
    // User is active
}
```

### Switch Status

```php
$user->switchStatus('inactive');

// Fires StatusChanged event
```

### Switch Status via Command

```bash
php artisan active-status:switch "App\Models\User" 1 "active"
```

## Events

The package fires a `StatusChanged` event when status changes:

```php
use Packages\ActiveStatus\Events\StatusChanged;

Event::listen(StatusChanged::class, function ($event) {
    $model = $event->model;
    $newStatus = $event->status;
    $previousStatus = $event->previousStatus;
    
    // Handle status change
});
```

## Service Provider

The service provider is auto-discovered. If needed, you can manually register it:

```php
'providers' => [
    Packages\ActiveStatus\ActiveStatusServiceProvider::class,
],
```
