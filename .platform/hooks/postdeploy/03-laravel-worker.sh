#!/usr/bin/bash -xe

# shutdown supervisor if it is running
echo "Shutting down Supervisor.."
supervisorctl shutdown

# Start supervisor
echo "Starting Supervisor.."
supervisord -c /etc/supervisord.conf

echo "Supervisor Started.."

# update supervisor config
echo "Updating Supervisor Config.."
supervisorctl update

# check supervisor status
echo "Checking Supervisor Status.."
supervisorctl status

# Start Laravel Queue Worker using Supervisor
# echo "Starting Laravel Queue Worker.."