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
