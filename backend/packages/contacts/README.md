# Contacts

A Laravel package for managing contact relationships (contactships) between Eloquent models.

## Installation

First, ensure that the package is located in `./packages/contacts` and registered in your `composer.json` repositories:

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
composer require sowidu/contacts:1.x-dev
```

## Setup

Publish the migrations:

```bash
php artisan vendor:publish --provider="Packages\Contacts\ContactServiceProvider" --tag=migrations
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

Add the contact functionality to your model using the repository:

```php
use Packages\Contacts\ContactshipRepository;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // ...
    
    public function contacts()
    {
        return new ContactshipRepository($this);
    }
}
```

### Add Contact

```php
$user = User::find(1);
$recipient = User::find(2);

use Packages\Contacts\Actions\AddContactAction;

// Add contact (sends request for User models, auto-accepts for others)
$contactship = (new AddContactAction($user->contacts()))->execute($recipient);

// Add contact and auto-accept
$contactship = (new AddContactAction($user->contacts()))->execute($recipient, true);
```

### Accept Request

```php
use Packages\Contacts\Actions\AcceptRequestAction;

$recipient = User::find(2);
$sender = User::find(1);

// Accept contact request
$result = (new AcceptRequestAction($recipient->contacts()))->execute($sender);
```

### Deny Request

```php
use Packages\Contacts\Actions\DenyRequestAction;

$result = (new DenyRequestAction($recipient->contacts()))->execute($sender);
```

### Cancel Request

```php
use Packages\Contacts\Actions\CancelRequestAction;

$result = (new CancelRequestAction($sender->contacts()))->execute($recipient);
```

### Block Contact

```php
use Packages\Contacts\Actions\BlockContactAction;

$result = (new BlockContactAction($user->contacts()))->execute($recipient);
```

### Unblock Contact

```php
use Packages\Contacts\Actions\UnblockContactAction;

$result = (new UnblockContactAction($user->contacts()))->execute($recipient);
```

### Check Contact Status

```php
// Check if users are contacts
$isContact = $user->contacts()->isContactWith($recipient);

// Check if user has blocked recipient
$hasBlocked = $user->contacts()->hasBlocked($recipient);

// Check if user is blocked by recipient
$isBlocked = $user->contacts()->isBlockedBy($recipient);

// Check if user has pending request from sender
$hasRequest = $user->contacts()->hasContactRequestFrom($sender);

// Check if user has sent request to recipient
$hasSentRequest = $user->contacts()->hasSentContactRequestTo($recipient);

// Get contact request
$contactship = $user->contacts()->getContactship($recipient);

// Get all contact requests
$requests = $user->contacts()->getContactRequests();
```

## Status Constants

```php
use Packages\Contacts\Status;

Status::PENDING;   // Contact request is pending
Status::ACCEPTED;  // Contact request is accepted
Status::DENIED;    // Contact request is denied
Status::BLOCKED;   // Contact is blocked
```

## Events

The package fires events for contact actions:

- `Packages\Contacts\Events\ContactRequestSent` - When a contact request is sent
- `Packages\Contacts\Events\ContactAccepted` - When a contact request is accepted
- `Packages\Contacts\Events\ContactRequestDenied` - When a contact request is denied
- `Packages\Contacts\Events\ContactRequestCancelled` - When a contact request is cancelled
- `Packages\Contacts\Events\ContactBlocked` - When a contact is blocked
- `Packages\Contacts\Events\ContactUnblocked` - When a contact is unblocked

## Service Provider

The service provider is auto-discovered. If needed, you can manually register it:

```php
'providers' => [
    Packages\Contacts\ContactServiceProvider::class,
],
```
