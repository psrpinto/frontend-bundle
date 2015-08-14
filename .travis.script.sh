#!/bin/bash
set -ev

if [[ $(phpenv version-name) = "5.6" && ( $SYMFONY_VERSION = "2.3.*" || $SYMFONY_VERSION = "2.7.*" ) ]]; then
    phpunit --coverage-text --coverage-clover coverage.xml
else
    phpunit
fi
