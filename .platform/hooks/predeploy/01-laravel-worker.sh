#!/usr/bin/bash

# shutdown supervisor if it is running
echo "Shutting down Supervisor.."
supervisorctl stop all

supervisorctl shutdown