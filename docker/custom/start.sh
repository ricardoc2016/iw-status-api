#!/usr/bin/env bash

echo "Starting IW Status API Environment...";

echo "Executing install.sh";

/development/install.sh;

echo "Adding php.ini...";

cp -f /development/docker/custom/php.ini /usr/local/etc/php/conf.d/php.ini;

touch /tmp/php_errors.log

chmod -R 777 /tmp/php_errors.log;

echo "Installing and enabling xdebug"

pecl install xdebug;

docker-php-ext-enable xdebug;

echo "Starting Apache...";

service apache2 start;

cd /development;

tail -F /development/var/logs/dev.log /development/var/logs/prod.log /tmp/php_errors.log;