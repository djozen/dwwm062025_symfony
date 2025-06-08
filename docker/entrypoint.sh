#!/bin/bash

# entrypoint.sh - Docker container entrypoint script

set -e

service php8.3-fpm start
apache2ctl -D FOREGROUND


# Note: This script starts the PHP-FPM service and Apache in the foreground.
# It is designed to be used as the entrypoint for a Docker container.
# Ensure that the script is executable:
# chmod +x docker/entrypoint.sh
# The script uses 'set -e' to exit immediately if any command fails.
# The 'exec' command replaces the shell with the specified command,
# allowing signals to be passed directly to the process.
# This is useful for proper shutdown handling in Docker containers.
# The script assumes that the services are installed and configured correctly
# in the Docker image.