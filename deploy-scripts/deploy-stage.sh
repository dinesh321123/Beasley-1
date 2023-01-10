#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

rsync -vrxc --delay-updates --delete-after ./payload/ beanstalk@34.195.141.227:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x
