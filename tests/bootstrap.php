<?php

namespace RevAPI;

$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add(__NAMESPACE__, __DIR__);

$config = array();
if (file_exists(__DIR__ . '/config.inc.php')) {
    $config = require __DIR__ . '/config.inc.php';
}

if (isset($config['REV_CLIENT_API_KEY'], $config['REV_USER_API_KEY'])) {
    putenv('REV_CLIENT_API_KEY=' . $config['REV_CLIENT_API_KEY']);
    putenv('REV_USER_API_KEY=' . $config['REV_USER_API_KEY']);
}

if (isset($config['TEST_VIDEO_URL'])) {
    putenv('TEST_VIDEO_URL='.$config['TEST_VIDEO_URL']);
} else {
    putenv('TEST_VIDEO_URL=http://mediahub.unl.edu/uploads/a07d73f214fe6bacbd446e6b90be8aa9.mp4');
}
