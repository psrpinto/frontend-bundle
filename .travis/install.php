#!/usr/bin/env php
<?php

include_once 'common.php';

runCommand('composer self-update');

if (isLatestPhp() && isLatestSymfony()) {
    // Make sure composer.json references all necessary components by having one
    // job run a `composer update`. Since `composer update` will install the
    // latest Symfony, this should be done for the job corresponding to the
    // latest symfony version.
    runCommand('composer update --prefer-dist');
} else {
    runCommand('composer require --prefer-dist symfony/symfony:'.getSymfonyVersion());
}
