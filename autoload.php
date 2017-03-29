<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

date_default_timezone_set('Europe/Paris');

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);