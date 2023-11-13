#!/usr/bin/bash

# Start supervisor
echo "Starting Supervisor.."
sudo supervisord -c /etc/supervisor/supervisord.conf

echo "Supervisor Started.."

# update supervisor config
echo "Updating Supervisor Config.."

sudo supervisorctl reread

sudo supervisorctl update

sudo supervisorctl restart all

# check supervisor status
echo "Checking Supervisor Status.."
sudo supervisorctl status

# Start Laravel Queue Worker using Supervisor
echo "Starting Laravel Queue Worker.."
