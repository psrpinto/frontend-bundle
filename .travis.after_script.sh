#!/bin/bash
set -ev

if [[ $(phpenv version-name) = "5.6" && ( $SYMFONY_VERSION = "2.3.*" || $SYMFONY_VERSION = "2.7.*" ) ]]; then
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover coverage.xml
fi
