#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

# Preprod lives in /var/www/html/preprod on the staging server
rsync -vrxc --delay-updates --delete-after ./ beanstalk@54.87.4.54:/var/www/html/preprod/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x
