#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

composer install

# removing Composer workflow from syndication plugin due to repos being removed
# version-controlling this for now, dependencies should be refactored and brought into the codebase if needed

# pushd plugins/greatermedia-content-syndication || exit 1
# composer install
# popd || exit 1

pushd themes || exit 1
npm install
npm run build
popd || exit 1

pushd themes/experience-engine || exit 1
npm install
npm run build
popd || exit 1

# Stop printing commands to screen
set +x
