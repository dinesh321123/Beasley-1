#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

# activate and display the node version set in the .nvmrc file
# nvm is very verbose so hide nvm use output
set +x
nvm use
set -x

node --version

# update composer to 2.x
composer self-update --2

composer install --no-dev -o

pushd themes/experience-engine || exit 1
npm install
npm run bundle
popd || exit 1

pushd themes/experience-engine-v2 || exit 1
npm install
npm run bundle
popd || exit 1

# Move plugins and theme to payload
mkdir -p payload
rsync -ravxc plugins mu-plugins themes vendor payload/ --exclude-from=./deploy-scripts/rsync-excludes.txt

# Stop printing commands to screen
set +x
