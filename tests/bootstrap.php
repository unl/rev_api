<?php

namespace RevAPI;

$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add(__NAMESPACE__, __DIR__);

if (file_exists(__DIR__ . '/config.inc.php')) {
    $config = require __DIR__ . '/config.inc.php';
    putenv('REV_CLIENT_API_KEY=' . $config['REV_CLIENT_API_KEY']);
    putenv('REV_USER_API_KEY=' . $config['REV_USER_API_KEY']);
}
