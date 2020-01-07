#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

# Pushes code to job server
rsync -vrxc --delete --delay-updates --delete-after ./ beanstalk@52.0.13.41:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x