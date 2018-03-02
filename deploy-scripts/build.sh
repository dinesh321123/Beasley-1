#!/bin/bash

# Print commands to the screen
set -x

# Set a local cache path for composer, so we can cache between builds and make things faster
composer config cache-files-dir .composer-cache

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
