#!/bin/bash

# Print commands to the screen
set -x

# Catch Errors
set -euo pipefail

composer install

<<<<<<< HEAD
pushd themes || exit 1
npm install
npm run build
popd || exit 1

=======
>>>>>>> feature/archive-old-themes
pushd themes/experience-engine || exit 1
npm install
npm run build
popd || exit 1

# Stop printing commands to screen
set +x
