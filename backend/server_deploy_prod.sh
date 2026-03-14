#!/bin/sh
set -e

echo "Deploying application ..."

# Enter maintenance mode
(php artisan down) || true
    # Update codebase
    git fetch origin prod-deploy
    git reset --hard origin/prod-deploy

    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    php artisan migrate --force
    php artisan optimize:clear
    php artisan auth:clear-resets
    php artisan queue:restart
	php artisan websocket:restart
    php artisan optimize
    php artisan event:cache
    php artisan app:sync-permissions
    php artisan horizon:terminate

    # Reload PHP to update opcache
    echo "" | sudo -S service php8.1-fpm reload

# Exit maintenance mode
php artisan up
echo "Application deployed!"
