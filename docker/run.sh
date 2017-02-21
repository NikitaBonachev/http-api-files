#!/bin/bash

composer install

if [ -n "${DEV_UID}" ]; then
    usermod -u ${DEV_UID} www-data
fi
if [ -n "${DEV_GID}" ]; then
    groupmod -g ${DEV_GID} www-data
fi

chown -R www-data:www-data /data
ulimit -s unlimited
rm -rf '/var/run/apache2'
service apache2 start

tail -f /dev/null
