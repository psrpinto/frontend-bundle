#!/usr/bin/env php
<?php

include_once 'common.php';

if (withCodeCoverage()) {
    runCommand('wget https://scrutinizer-ci.com/ocular.phar');
    runCommand('php ocular.phar code-coverage:upload --format=php-clover coverage.xml');
}
