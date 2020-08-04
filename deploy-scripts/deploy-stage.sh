#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

rsync -vrxc --delay-updates --delete-after ./ beanstalk@54.87.4.54:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x
