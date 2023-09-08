#!/usr/bin/bash
echo "Making /storage writeable..."
chmod -R 755 /var/app/current/storage

touch /var/app/current/storage/logs/laravel.log
chown webapp:webapp /var/app/current/storage/logs/laravel.log
chmod 644 /var/app/current/storage/logs/laravel.log

touch /var/log/supervisord.log
chown webapp:webapp /var/log/supervisord.log
chmod 644 /var/log/supervisord.log

touch /var/log/laravel-worker.log
chown webapp:webapp /var/log/laravel-worker.log
chmod 644 /var/log/laravel-worker.log
