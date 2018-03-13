#!/bin/bash

rsync -vrxc --delete ./themes/ beanstalk@52.0.13.41:/var/www/html/wordpress/wp-content/themes/ --exclude-from=./deploy-scripts/rsync-excludes.txt
rsync -vrxc --delete ./plugins/ beanstalk@52.0.13.41:/var/www/html/wordpress/wp-content/plugins/ --exclude-from=./deploy-scripts/rsync-excludes.txt
rsync -vrxc --delete ./mu-plugins/ beanstalk@52.0.13.41:/var/www/html/wordpress/wp-content/mu-plugins/ --exclude-from=./deploy-scripts/rsync-excludes.txt
