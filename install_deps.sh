#!/bin/bash

pushd .
cd plugins/greatermedia-gigya
composer install --no-dev --optimize-autoloader --no-interaction
popd

pushd .
cd plugins/greatermedia-livefyre
composer install --no-dev --optimize-autoloader --no-interaction
popd
