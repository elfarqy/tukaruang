cd /var/www/html/backend
composer install
php init --env=Production --overwrite=All
php yii migrate --interactive=0
php yii generator/data

apache2-foreground