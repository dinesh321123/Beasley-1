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

composer install --no-dev -o

# removing Composer workflow from syndication plugin due to repos being removed
# version-controlling this for now, dependencies should be refactored and brought into the codebase if needed

# pushd plugins/greatermedia-content-syndication || exit 1
# composer install --no-dev -o
# popd || exit 1

pushd themes || exit 1
npm install
npm run build
popd || exit 1

pushd themes/experience-engine || exit 1
npm install
npm run bundle
popd || exit 1

# Stop printing commands to screen
set +x
