#!/usr/bin/env php
<?php

include_once 'common.php';

if (!isHhvm() && !withCodeCoverage()) {
    // Disable XDebug
    runCommand('phpenv config-rm xdebug.ini');
}
