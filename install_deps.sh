#!/bin/bash

pushd .
cd plugins/greatermedia-gigya
composer install
popd

pushd .
cd plugins/greatermedia-timed-content
bower install
popd

pushd .
cd plugins/greatermedia-gigya
composer update --no-dev --optimize-autoloader --no-interaction
popd

pushd .
cd plugins/greatermedia-livefyre
composer update --no-dev --optimize-autoloader --no-interaction
popd

