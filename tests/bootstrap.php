<?php

namespace RevAPI;

define('FIXTURES_DIR', __DIR__ . '/fixtures');

$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add(__NAMESPACE__, __DIR__);
