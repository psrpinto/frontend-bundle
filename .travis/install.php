#!/usr/bin/env php
<?php

include_once 'common.php';

if (isLatestPhp() && isLatestSymfony()) {
    // Make sure composer.json references all necessary components by having one
    // job run a `composer update`. Since `composer update` will install the
    // latest Symfony, this should be done for the job corresponding to the
    // latest symfony version.
    runCommand('composer update --prefer-dist');
} else {
    runCommand('composer require --prefer-dist symfony/symfony:'.getSymfonyVersion());
}

if (shouldBuildDocs()) {
    runCommand('export PATH=$HOME/.local/bin:$PATH && pip install -r requirements.txt --user `whoami`');
}
