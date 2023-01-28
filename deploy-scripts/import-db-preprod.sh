#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

ssh beanstalk@52.0.13.41 'rsync -vrxc /home/beanstalk/preprod_dbs preprod:/home/beanstalk/ --dry-run'

set +x
