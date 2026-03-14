### Catalog

Catalog is a Laravel module that provides the **catalog items** feature (items CRUD + listing UI via Inertia).

#### Routes

- **Module routes**: `modules/catalog/routes/web.php`
- **Prefix**: configured in `modules/catalog/config/catalog.php` (currently `catalog`)
- **Registration**: `modules/catalog/src/CatalogServiceProvider.php` loads routes using config prefix/middleware.

#### External Contracts (Ports & Adapters)

This module follows the **external contracts** pattern: module code depends on interfaces under `modules/catalog/src/Contracts/External/`, implemented by adapters in the main app and bound in the container.

**Contracts (module)**:
- `Modules\Catalog\Contracts\External\MediaManagerContract`
- `Modules\Catalog\Contracts\External\PermissionContract`
- `Modules\Catalog\Contracts\External\CompanyInfoContract`

**Adapters (main app)**:
- `App\Services\Catalog\MediaManagerAdapter`
- `App\Services\Catalog\PermissionAdapter`
- `App\Services\Catalog\CompanyInfoAdapter`

**Bindings (main app)**:
- `app/Providers/CatalogServiceProvider.php` binds the contracts to adapters
- `config/app.php` registers `App\Providers\CatalogServiceProvider`

#### Where the module uses the contracts

- **Media lookup / ownership**
  - `modules/catalog/src/Actions/Concerns/ValidatesCatalogItems.php`
  - `modules/catalog/src/Actions/CreateCatalogItem.php`
- **Permission checks & currency**
  - `modules/catalog/src/Http/Controllers/CatalogItemController.php`

#### Testing

Run module tests:

```bash
sail artisan test ./modules/catalog/tests
```
