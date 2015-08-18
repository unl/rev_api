<?php

namespace RevAPI;

$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add(__NAMESPACE__, __DIR__);

if (file_exists(__DIR__ . '/config.inc.php')) {
    require_once __DIR__ . '/config.inc.php';
}
