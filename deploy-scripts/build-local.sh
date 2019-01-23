#!/bin/bash

# Print commands to the screen
set -x

composer install

pushd plugins/greatermedia-content-syndication || exit 1
composer install
popd || exit 1

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
