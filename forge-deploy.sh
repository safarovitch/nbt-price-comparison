#!/bin/bash

# Laravel Forge Deploy Script
# This script is designed to be used as the deployment script in Laravel Forge.

cd /home/forge/your-domain.com

# Pull latest changes
git pull origin $FORGE_SITE_BRANCH

# Install composer dependencies
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install npm dependencies and build assets
npm ci
npm run build

# Run migrations
$FORGE_PHP artisan migrate --force

# Clear and rebuild caches
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan event:cache

# Restart Horizon (queue worker)
$FORGE_PHP artisan horizon:terminate

# Restart the application
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmrestart.lock
