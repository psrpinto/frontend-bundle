#!/bin/bash
set -ev

if [[ $(phpenv version-name) = "7.0" && ( $SYMFONY_VERSION = "2.3.*" || $SYMFONY_VERSION = "2.8.*" ) ]]; then
    phpunit --coverage-text --coverage-clover coverage.xml
else
    phpunit
fi
