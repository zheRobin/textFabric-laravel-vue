#!/usr/bin/bash -xe
echo "Making /storage writeable..."
chmod -R 755 /var/app/current/storage

if [ ! -f /var/app/current/storage/logs/laravel.log ]; then
    echo "Creating /storage/logs/laravel.log..."
    touch /var/app/current/storage/logs/laravel.log
    chown webapp:webapp /var/app/current/storage/logs/laravel.log
    chmod 644 /var/app/current/storage/logs/laravel.log
fi 

chown webapp:webapp /var/app/current/storage/worker.log
chmod 644 /var/app/current/storage/worker.log