<?php
require __DIR__ . '/../../config.php';
/**
 * @var \Composer\Autoload\ClassLoader $autoLoader
 */
$autoLoader = require VENDOR_ROOT . '/autoload.php';
$autoLoader->add('KingO', SRC_ROOT);

require __DIR__ . '/CMSDashboardKernel.php';

(new CMSDashboardKernel($autoLoader))
    ->addConfigFiles([
        realpath(__DIR__ . '/../config.yml'),
        realpath(__DIR__ . '/../db.yml'),
        realpath(__DIR__ . '/../web.yml'),
        realpath(__DIR__ . '/../dashboard.config.yml'),
    ])
    ->boot()
    ->startup();
