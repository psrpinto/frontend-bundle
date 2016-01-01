#!/bin/bash
set -ev

if [[ $(phpenv version-name) = "7.0" && ( $SYMFONY_VERSION = "2.3.*" || $SYMFONY_VERSION = "2.8.*" ) ]]; then
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover coverage.xml
fi
