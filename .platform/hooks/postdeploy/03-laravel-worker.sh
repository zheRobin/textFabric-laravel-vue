#!/usr/bin/bash

# Start supervisor
echo "Starting Supervisor.."
supervisord -c /etc/supervisor/supervisord.conf

echo "Supervisor Started.."

# update supervisor config
echo "Updating Supervisor Config.."

supervisorctl reread

supervisorctl update

# check supervisor status
echo "Checking Supervisor Status.."
supervisorctl status

# Start Laravel Queue Worker using Supervisor
echo "Starting Laravel Queue Worker.."