#!/bin/bash
WP_PATH="/var/www/html/wordpress"
# Do not Print commands to the screen
set +x

# Catch Errors
set -euo pipefail

# Generate search-replace script to run on preprod
SEARCH_REPLACE=(
  "wp search-replace 'content.bbgi.com' 'content-preprod.bbgistage.com' --url=https://content-preprod.bbgistage.com/ --all-tables-with-prefix"
  "wp search-replace 'wmmr.com' 'wmmr-preprod.bbgistage.com' --url=https://wmmr-preprod.bbgistage.com/ --all-tables-with-prefix"
  "wp search-replace 'wrif.com' 'wrif-preprod.bbgistage.com' --url=https://wrif-preprod.bbgistage.com/ --all-tables-with-prefix"
  "wp search-replace '985thesportshub.com' '985thesportshub-preprod.bbgistage.com' --url=https://985thesportshub-preprod.bbgistage.com/ --all-tables-with-prefix"
  "wp search-replace 'jammin1057.com' 'jammin1057-preprod.bbgistage.com' --url=https://jammin1057-preprod.bbgistage.com/ --all-tables-with-prefix"
  "wp cache flush --network"
)

# Import .sql files into preprod
SITES=$(ssh beanstalk@34.230.103.178 'ls preprod_backups')
for SITE in ${SITES};
do
  SITE_PREFIX=$(echo $SITE|cut -d'.' -f1)
  SITE_NAME=$(echo $SITE|cut -d'.' -f1,2)
  ssh beanstalk@34.230.103.178 "wp db import ~/preprod_backups/${SITE_NAME}.sql --path=${WP_PATH} --url=https://${SITE_PREFIX}-preprod.bbgistage.com/"
done

# SSH to preprod and run search-replace
for run_sr in "${SEARCH_REPLACE[@]}"; do
  ssh beanstalk@34.230.103.178 "cd $WP_PATH && $run_sr"
done
