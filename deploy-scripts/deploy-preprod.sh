#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

rsync -vrxc --delay-updates --delete-after ./ beanstalk@34.230.103.178:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x
