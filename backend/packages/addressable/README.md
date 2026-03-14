# Addressable

A Laravel package for managing addresses on Eloquent models.

## Installation

First, ensure that the package is located in `./packages/addressable` and registered in your `composer.json` repositories:

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
composer require sowidu/addressable:1.x-dev
```

## Setup

Publish the configuration and migrations:

```bash
php artisan vendor:publish --provider="Packages\Addressable\AddressServiceProvider" --tag=config
php artisan vendor:publish --provider="Packages\Addressable\AddressServiceProvider" --tag=migrations
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

Add the `HasAddresses` trait to your model:

```php
use Packages\Addressable\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasAddresses;
    
    // ...
}
```

### Add Address

```php
$user = User::find(1);

// Add address with data
$user->address()->add('home', [
    'street' => '123 Main St',
    'city' => 'New York',
    'state' => 'NY',
    'zip' => '10001',
    'country' => 'United States',
]);

// Or create directly
$user->address()->create([
    'name' => 'work',
    'street' => '456 Business Ave',
    'city' => 'New York',
    'state' => 'NY',
    'zip' => '10002',
    'country' => 'United States',
]);
```

### Get Addresses

```php
// Get all addresses
$addresses = $user->address()->all();

// Get newest address
$newest = $user->address()->newestFirst();

// Get oldest address
$oldest = $user->address()->oldestFirst();

// Find by name
$homeAddress = $user->address()->findByName('home');

// Get relationship
$addresses = $user->addresses;
```

### Delete Addresses

```php
// Delete by name
$user->address()->delete('home');

// Delete by ID
$user->address()->delete(1);

// Delete all addresses
$user->address()->deleteAll();
```

## Validation Rules

The package includes validation rules for countries and states:

```php
use Packages\Addressable\Rules\CountryRule;
use Packages\Addressable\Rules\StateRule;

$request->validate([
    'country' => ['required', new CountryRule()],
    'state' => ['required', new StateRule($countryCode)],
]);
```

## Service Provider

The service provider is auto-discovered. If needed, you can manually register it:

```php
'providers' => [
    Packages\Addressable\AddressServiceProvider::class,
],
```
