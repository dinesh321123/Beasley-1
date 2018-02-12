#!/bin/bash

# Print commands to the screen
set -x

composer install --no-dev -o

pushd plugins/greatermedia-content-syndication || exit 1
composer install --no-dev -o
popd || exit 1

pushd themes || exit 1
npm install
npm run build
popd || exit 1

# Stop printing commands to screen
set +x
