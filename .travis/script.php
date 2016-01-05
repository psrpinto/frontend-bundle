#!/usr/bin/env php
<?php

include_once 'common.php';

if (withCodeCoverage()) {
    runCommand('phpunit --coverage-text --coverage-clover coverage.xml');
} else {
    runCommand('phpunit');
}
