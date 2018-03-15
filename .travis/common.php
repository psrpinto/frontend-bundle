<?php

function shouldBuildDocs()
{
    // disable due to SSL error on travis
    return false;
    return isLatestPhp() && isLatestSymfony();
}

function withCodeCoverage()
{
    return isLatestPhp() && isLatestSymfony();
}

function isLatestPhp()
{
    return getPhpVersion() === '7.2';
}

function isLatestSymfony()
{
    return getSymfonyVersion() === '3.4.*';
}

function getSymfonyVersion()
{
    return getenv('SYMFONY_VERSION');
}

function getPhpVersion()
{
    return exec('phpenv version-name');
}

function runCommand($command)
{
    echo "$ $command\n";

    passthru($command, $ret);

    if ($ret !== 0) {
        exit($ret);
    }
}
