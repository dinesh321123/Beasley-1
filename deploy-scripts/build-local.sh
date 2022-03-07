#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

composer install

pushd themes/experience-engine || exit 1
npm install
npm run build
popd || exit 1

pushd themes/experience-engine-v2 || exit 1
npm install
npm run build
popd || exit 1

pushd themes || exit 1
npm install
popd || exit 1

# Stop printing commands to screen
set +x
