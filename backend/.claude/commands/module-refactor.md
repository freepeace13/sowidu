# Refactor to Module

Move existing code from the main app into a module following the Ports & Adapters pattern.

## Instructions

When this command is invoked (e.g., `/module-refactor addressbook`):

1. **Analyze the feature** in the main app:
   - Controllers in `app/Http/Controllers/`
   - Models in `app/Models/`
   - Actions in `app/Actions/`
   - Services in `app/Services/`
   - Events in `app/Events/`
   - Routes in `routes/`

2. **Identify external dependencies**:
   - Which main app models are used?
   - Which services are called?
   - What authorization is needed?

3. **Create migration plan**:
   - What moves to the module
   - What stays in main app (as adapters)
   - What contracts need to be defined

4. **Execute the refactoring** step by step

## Refactoring Checklist

### Step 1: Create Module Structure
- [ ] Create module folder using `/module-create {name}`
- [ ] Set up composer.json and service provider

### Step 2: Move Models
- [ ] Move models to `modules/{name}/src/Models/`
- [ ] Update namespace to `Modules\{Name}\Models`
- [ ] Update any morphMap in service provider

### Step 3: Define External Contracts
For each main app dependency, create a contract:
- [ ] `src/Contracts/External/UserContract.php` (if needs User)
- [ ] `src/Contracts/External/AuthorizationContract.php` (if needs Gate)
- [ ] `src/Contracts/External/MediaContract.php` (if needs media)

### Step 4: Move Actions
- [ ] Move actions to `modules/{name}/src/Actions/`
- [ ] Create action contracts in `src/Contracts/Actions/`
- [ ] Replace `App\Models\*` imports with external contracts
- [ ] Register bindings in service provider

### Step 5: Move Controllers
- [ ] Move controllers to `modules/{name}/src/Http/Controllers/`
- [ ] Update namespace and imports
- [ ] Inject contracts instead of concrete classes

### Step 6: Move Routes
- [ ] Copy routes to `modules/{name}/routes/web.php`
- [ ] Remove from main app routes
- [ ] Update controller references

### Step 7: Create Adapters in Main App
- [ ] Create `app/Services/{Name}/` folder
- [ ] Implement adapters for each external contract
- [ ] Register bindings in `app/Providers/{Name}ServiceProvider.php`

### Step 8: Move Supporting Code
- [ ] Events to `src/Events/`
- [ ] Policies to `src/Policies/`
- [ ] Transformers to `src/Transformers/`
- [ ] Move migrations

### Step 9: Update Tests
- [ ] Move relevant tests to `modules/{name}/tests/`
- [ ] Update namespaces and imports
- [ ] Ensure all tests pass

### Step 10: Cleanup
- [ ] Remove old files from main app
- [ ] Run `composer dump-autoload`
- [ ] Test the module independently

## Example: Refactoring Addressbook

```
Main App (before)                    Module (after)
─────────────────                    ──────────────
app/Models/Addressbook.php      →   modules/addressbook/src/Models/Addressbook.php
app/Actions/Addressbook/*       →   modules/addressbook/src/Actions/*
app/Http/Controllers/           →   modules/addressbook/src/Http/Controllers/
  Inertia/Addressbook/*
routes/addressbook.php          →   modules/addressbook/routes/web.php

NEW: modules/addressbook/src/Contracts/External/
     - UserSearchContract.php
     - MediaManagerContract.php
     - CompanyInfoContract.php

NEW: app/Services/Addressbook/
     - UserSearchAdapter.php (implements UserSearchContract)
     - MediaManagerAdapter.php
     - CompanyInfoAdapter.php
```

## Critical Rules

1. **Never import `App\` in modules** - always use contracts
2. **Keep adapters thin** - they just delegate to main app services
3. **Test isolation** - module tests should not depend on main app state
4. **One feature at a time** - don't try to refactor everything at once
