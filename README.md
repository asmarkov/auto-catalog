composer update

./vendor/bin/sail up

php artisan make:command auto-catalog:sync

Путь указывается от storage/app - например (по умолчанию) public/data.xml

