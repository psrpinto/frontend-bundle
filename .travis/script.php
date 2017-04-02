#!/usr/bin/env php
<?php

include_once 'common.php';

if (withCodeCoverage()) {
    runCommand('vendor/bin/phpunit --coverage-text --coverage-clover coverage.xml');
} else {
    runCommand('vendor/bin/phpunit');
}

if (shouldBuildDocs()) {
    runCommand('sphinx-build -E -W Resources/doc Resources/doc/_build');
}
