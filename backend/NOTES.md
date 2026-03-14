## Developers notes

### For adding new `**Permission**`

-   Add new permission on the `config/app.php` -> `defaults.permissions`
-   Add new permission on `app/Http/Middleware/HandleInertiaRequests.php` - `$defaultPermissions` variable.
-   Add `permission` middleware on specific routes
-   Add new permission on `app/Enums/Permissions.php`
-   Run `php artisan app:sync-permissions` _(This will add new permission on the database)_ and Set **default** permission on Company/Organization founder/owner.

pdfjs-dist
"pdfjs-dist": "3.10.111",
