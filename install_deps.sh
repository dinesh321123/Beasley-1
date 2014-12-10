#!/bin/bash

pushd .
cd plugins/greatermedia-gigya
composer install
popd

pushd .
cd plugins/greatermedia-contests
bower install
popd

pushd .
cd plugins/greatermedia-timed-content
bower install
popd
