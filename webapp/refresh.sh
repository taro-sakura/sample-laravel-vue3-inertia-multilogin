COMPOSER_MEMORY_LIMIT=-1 composer install --no-scripts
php artisan migrate --force
php artisan migrate --force --env=testing
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
php artisan optimize
php artisan config:cache
composer dump-autoload
