#!/bin/bash

rsync -vrxc --delete --delay-updates --delete-after ./ beanstalk@54.87.4.54:/var/www/html/wordpress/wp-content/ --exclude-from=./deploy-scripts/rsync-excludes.txt
