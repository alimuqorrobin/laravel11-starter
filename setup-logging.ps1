# Install logging libraries
composer require spatie/laravel-activitylog laravel/telescope opcodesio/log-viewer

# Publish configs & migrations
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
php artisan telescope:install
php artisan vendor:publish --tag=log-viewer-config

# Jalankan migrate
php artisan migrate

# Clear cache
php artisan optimize:clear

Write-Output "✅ Logging system installed (Activitylog, Telescope, Log Viewer)"
