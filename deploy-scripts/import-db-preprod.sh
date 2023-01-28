#!/bin/bash
WP_PATH="/var/www/html/wordpress"

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

# Sync .sql files from jobs to preprod
ssh beanstalk@52.0.13.41 'rsync -vrxc preprod_backups preprod:~/'

# Import .sql files into preprod
SITES=$(ssh beanstalk@34.230.103.178 'ls preprod_backups')
for SITE in ${SITES};
do
  #ssh beanstalk@34.230.103.178 'wp --ssh=preprod:${WP_PATH} db import ~/preprod_backups/${SITE} --path=${WP_PATH}
  echo ${SITE}
done

set +x

