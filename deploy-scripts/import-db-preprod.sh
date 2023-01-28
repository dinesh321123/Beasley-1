#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

ssh beanstalk@52.0.13.41 'rsync -vrxc preprod_backups preprod:~/ --dry-run'
#ssh beanstalk@52.0.13.41 'pwd'
set +x
