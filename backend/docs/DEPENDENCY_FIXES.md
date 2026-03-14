# Dependency Fixes for Monorepo

This document tracks dependency issues and their resolutions during the monorepo restructuring.

## Issues Fixed

### 1. GitHub Authentication (2024-12-10)
**Problem**: Invalid GitHub token causing authentication failures
**Solution**: 
- Updated `auth.json` with placeholder
- Added `auth.json` to `.gitignore`
- Created `docs/GITHUB_AUTH_SETUP.md` with setup instructions

### 2. php-ffmpeg PHP Version Conflict (2024-12-10)
**Problem**: 
- `php-ffmpeg/php-ffmpeg: ^1.1` was resolving to `v1.x-dev` which requires `php ^7.1`
- System has PHP 8.1.32, causing dependency conflict

**Solution**: 
- Updated constraint to `^1.3` to use stable versions that support PHP 8.1
- Latest stable version `v1.3.2` supports PHP 8.1+

**Files Changed**:
- `composer.json`: Updated `php-ffmpeg/php-ffmpeg` from `^1.1` to `^1.3`

### 3. Alchemy Zippy Local Module (2024-12-10)
**Problem**: 
- `alchemy/zippy: 1.x-dev` was pointing to VCS repository (https://github.com/rj-wamal/Zippy.git)
- Local copy exists in `modules/zippy/` but composer was preferring VCS repository
- Local module had outdated symfony/process constraints (^3.4 || ^4.0 || ^5.0) conflicting with php-ffmpeg

**Solution**: 
- Changed constraint to `@dev` to allow local path repository
- Added explicit path repository for `./modules/zippy` BEFORE the wildcard `./modules/*` pattern
- Updated `modules/zippy/composer.json`:
  - PHP requirement: `>=7.1` → `^8.0`
  - symfony/filesystem: `^2.0.5 || ^3.0 || ^4.0 || ^5.0` → `^5.0 || ^6.0`
  - symfony/process: `^3.4 || ^4.0 || ^5.0` → `^5.4 || ^6.0` (compatible with php-ffmpeg)
  - symfony/finder: `^2.0.5 || ^3.0 || ^4.0 || ^5.0` → `^5.0 || ^6.0`

**Files Changed**:
- `composer.json`: 
  - Updated `alchemy/zippy` from `1.x-dev` to `@dev`
  - Added explicit path repository for `./modules/zippy` (before wildcard pattern)
- `modules/zippy/composer.json`: Updated all Symfony dependencies to support PHP 8.1

## Testing

After fixes, run:
```bash
./vendor/bin/sail composer install
```

If issues persist:
1. Clear composer cache: `./vendor/bin/sail composer clear-cache`
2. Remove vendor directory: `rm -rf vendor`
3. Reinstall: `./vendor/bin/sail composer install`

## Notes

- Always use `./vendor/bin/sail` prefix for composer commands (Laravel Sail)
- `minimum-stability: dev` allows dev versions, but prefer stable when possible
- Local modules should use `@dev` constraint and path repositories

---

**Last Updated**: 2024-12-10

