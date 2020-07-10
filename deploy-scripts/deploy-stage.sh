#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

# adding --dry-run flag until stage2 branch is ready to use for stage deploys
rsync --dry-run -vrxc --delay-updates --delete-after ./ beanstalk@54.87.4.54:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt

set +x
