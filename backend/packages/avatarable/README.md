# Avatarable

A Laravel package for managing avatars on Eloquent models.

## Installation

First, ensure that the package is located in `./packages/avatarable` and registered in your `composer.json` repositories:

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
composer require sowidu/avatarable:1.x-dev
```

## Setup

Publish the configuration, migrations, and default avatar image:

```bash
php artisan vendor:publish --provider="Packages\Avatarable\AvatarServiceProvider" --tag=config
php artisan vendor:publish --provider="Packages\Avatarable\AvatarServiceProvider" --tag=migrations
php artisan vendor:publish --provider="Packages\Avatarable\AvatarServiceProvider" --tag=images
```

Run the migrations:

```bash
php artisan migrate
```

Configure your filesystem disk and directory in `config/avatar.php`:

```php
return [
    'disk' => env('AVATAR_DISK', 'public'),
    'directory' => env('AVATAR_DIRECTORY', 'avatars'),
];
```

## Usage

Add the `HasAvatar` trait to your model:

```php
use Packages\Avatarable\Traits\HasAvatar;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasAvatar;
    
    // ...
}
```

### Set Avatar

```php
$user = User::find(1);

// Set avatar from uploaded file
$avatar = $user->setAvatar($request->file('avatar'));

// Set avatar from file path
$avatar = $user->setAvatar('/path/to/image.jpg');

// Set avatar with custom disk
$avatar = $user->setAvatar($request->file('avatar'), 's3');
```

### Get Avatar

```php
// Get avatar URL
$avatarUrl = $user->avatar->getUrl();

// Get avatar path
$avatarPath = $user->avatar->getPath();

// Get default avatar if no avatar is set
$defaultAvatar = $user->avatar->getDefaultPath();

// Set custom default path
$user->avatar->setDefaultPath('images/default-avatar.png');
```

### Avatar Model

```php
$avatar = $user->avatar;

// Get file name
$fileName = $avatar->file_name;

// Get disk
$disk = $avatar->disk;

// Get directory
$directory = $avatar->getDirectory();

// Get disk driver name
$driver = $avatar->getDiskDriverName();
```

## Service Provider

The service provider is auto-discovered. If needed, you can manually register it:

```php
'providers' => [
    Packages\Avatarable\AvatarServiceProvider::class,
],
```

## Exceptions

The package throws specific exceptions:

- `Packages\Avatarable\Exceptions\FileDoesNotExist` - When file doesn't exist
- `Packages\Avatarable\Exceptions\DiskDoesNotExist` - When filesystem disk doesn't exist
- `Packages\Avatarable\Exceptions\UnknownType` - When file type is not supported
