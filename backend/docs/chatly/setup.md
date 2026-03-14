# Chatly Setup Instructions

This guide explains how to complete the setup after implementing the Chatly external adapters.

## ✅ Completed

The following adapter implementations have been created:

```
app/Services/Chat/
├── UserSearchAdapter.php      ✓ Created
├── UrnResolverAdapter.php     ✓ Created
├── MediaManagerAdapter.php    ✓ Created
├── AuthorizationAdapter.php   ✓ Created
├── BroadcasterAdapter.php     ✓ Created
└── UserDisplayAdapter.php     ✓ Created

app/Providers/
└── ChatServiceProvider.php    ✓ Created
```

## 📋 Required Step: Register Service Provider

Add `ChatServiceProvider` to your application's service providers list.

### Option 1: Auto-Discovery (Laravel 5.5+)

If using auto-discovery, add to `composer.json`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "App\\Providers\\ChatServiceProvider"
            ]
        }
    }
}
```

Then run:
```bash
composer dump-autoload
```

### Option 2: Manual Registration

Add to `config/app.php`:

```php
'providers' => [
    // ... other providers

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // ... other providers
    
    // Chatly External Dependencies
    App\Providers\ChatServiceProvider::class,
],
```

## 🧪 Verify Installation

Run the following command to verify the bindings are registered:

```bash
php artisan tinker
```

Then test:

```php
// Test UserSearchContract binding
$userSearch = app(\Modules\Chatly\Contracts\External\UserSearchContract::class);
echo get_class($userSearch);
// Should output: App\Services\Chat\UserSearchAdapter

// Test UrnResolverContract binding
$urnResolver = app(\Modules\Chatly\Contracts\External\UrnResolverContract::class);
echo get_class($urnResolver);
// Should output: App\Services\Chat\UrnResolverAdapter

// Test all bindings
$contracts = [
    \Modules\Chatly\Contracts\External\UserSearchContract::class,
    \Modules\Chatly\Contracts\External\UrnResolverContract::class,
    \Modules\Chatly\Contracts\External\MediaManagerContract::class,
    \Modules\Chatly\Contracts\External\AuthorizationContract::class,
    \Modules\Chatly\Contracts\External\BroadcasterContract::class,
    \Modules\Chatly\Contracts\External\UserDisplayContract::class,
];

foreach ($contracts as $contract) {
    $adapter = app($contract);
    echo basename(str_replace('\\', '/', $contract)) . ' => ' . get_class($adapter) . "\n";
}
```

Expected output:
```
UserSearchContract => App\Services\Chat\UserSearchAdapter
UrnResolverContract => App\Services\Chat\UrnResolverAdapter
MediaManagerContract => App\Services\Chat\MediaManagerAdapter
AuthorizationContract => App\Services\Chat\AuthorizationAdapter
BroadcasterContract => App\Services\Chat\BroadcasterAdapter
UserDisplayContract => App\Services\Chat\UserDisplayAdapter
```

## 🔄 Clear Caches

After registration, clear application caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 🧩 Adapter Overview

### UserSearchAdapter
- **Implements**: `UserSearchContract`
- **Wraps**: `App\Models\User`, `App\Models\Employee`
- **Methods**: `search()`, `findByUrn()`, `currentUser()`, `currentTeam()`
- **Used by**: SearchController, CreateConversation, AddParticipant

### UrnResolverAdapter
- **Implements**: `UrnResolverContract`
- **Wraps**: `Packages\Urn\UrnManager`
- **Methods**: `resolve()`, `generate()`, `isValid()`, `parse()`
- **Used by**: AddParticipant, ParticipantResource

### MediaManagerAdapter
- **Implements**: `MediaManagerContract`
- **Wraps**: `Packages\MediaLibrary`, `App\Transformers\MediaTransformer`
- **Methods**: `store()`, `find()`, `getMediaForUser()`, `delete()`, `getAllowedMimeTypes()`
- **Used by**: MessageProvider, AttachmentBuilder

### AuthorizationAdapter
- **Implements**: `AuthorizationContract`
- **Wraps**: Laravel Gates, Policies, Permissions
- **Methods**: `can()`, `authorize()`, `hasPermission()`
- **Used by**: All Actions and Controllers

### BroadcasterAdapter
- **Implements**: `BroadcasterContract`
- **Wraps**: Laravel Broadcasting system
- **Methods**: `broadcast()`, `broadcastToConversation()`, `broadcastToUser()`, `isEnabled()`
- **Used by**: ChatBroadcaster, MessageProvider

### UserDisplayAdapter
- **Implements**: `UserDisplayContract`
- **Wraps**: Helper functions (`get_user_avatar_url()`, `get_company_avatar_url()`)
- **Methods**: `getAvatarUrl()`, `getCompanyAvatarUrl()`, `getDisplayName()`, `isTeamMember()`
- **Used by**: ParticipantResource, ConversationProvider

## 📖 Next Steps

1. ✅ **Register service provider** (see above)
2. ⏳ **Refactor Chatly code** to use contracts instead of direct dependencies
3. ⏳ **Update tests** to mock contracts
4. ⏳ **Remove direct imports** of `App\Models\*` from Chatly module

See the following documentation for detailed refactoring guides:
- `modules/chatly/INTEGRATION_GUIDE.md` - Step-by-step integration guide
- `modules/chatly/docs/external-contracts.md` - Contract specifications
- `modules/chatly/docs/architecture.md` - Architecture overview

## 🐛 Troubleshooting

### Issue: "Target class does not exist"
**Error**: `Target class [Modules\Chatly\Contracts\External\UserSearchContract] does not exist.`

**Solution**: 
1. Verify `ChatServiceProvider` is registered in `config/app.php`
2. Run `php artisan config:clear`
3. Run `composer dump-autoload`

### Issue: "Class not found" in adapters
**Error**: `Class 'App\Models\User' not found`

**Solution**: Verify all model classes exist and are properly namespaced.

### Issue: Helper functions not found
**Error**: `Call to undefined function get_user_avatar_url()`

**Solution**: These helpers should be defined in `app/Helpers.php` or loaded via Composer.

### Issue: MediaTransformer not working
**Error**: MediaTransformer errors

**Solution**: Verify `App\Transformers\MediaTransformer` exists and MediaLibrary is properly installed.

## ✅ Testing the Setup

Create a simple test route to verify everything works:

```php
// routes/web.php or routes/api.php
Route::get('/test-chatly-adapters', function () {
    $userSearch = app(\Modules\Chatly\Contracts\External\UserSearchContract::class);
    $urnResolver = app(\Modules\Chatly\Contracts\External\UrnResolverContract::class);
    
    return response()->json([
        'status' => 'ok',
        'adapters' => [
            'UserSearchAdapter' => get_class($userSearch),
            'UrnResolverAdapter' => get_class($urnResolver),
        ],
    ]);
});
```

Access the route and verify you get:
```json
{
    "status": "ok",
    "adapters": {
        "UserSearchAdapter": "App\\Services\\Chat\\UserSearchAdapter",
        "UrnResolverAdapter": "App\\Services\\Chat\\UrnResolverAdapter"
    }
}
```

## 📚 Additional Resources

- Laravel Service Providers: https://laravel.com/docs/providers
- Dependency Injection: https://laravel.com/docs/container
- Hexagonal Architecture: https://alistair.cockburn.us/hexagonal-architecture/

## 🎉 Success!

Once the service provider is registered and verified, the Chatly module will have proper access to external dependencies through well-defined contracts, following clean architecture principles.

