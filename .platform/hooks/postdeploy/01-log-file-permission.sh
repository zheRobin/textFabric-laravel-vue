#!/usr/bin/bash
echo "Giving Permission to Laravel Log file.."
sudo touch /var/app/current/storage/logs/laravel.log
# sudo chown $USER:webapp /var/app/current/storage/logs/laravel.log
sudo chown -R $USER:webapp /var/app/current/storage
sudo chmod -R 755 $USER:webapp /var/app/current/storage