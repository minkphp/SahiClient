<?php

if (!is_dir($vendor = __DIR__.'/../vendor')) {
    die('Install dependencies first');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require($vendor.'/autoload.php');

$loader->addPsr4('Test\Behat\SahiClient\\', __DIR__.'/Behat/SahiClient');
