<?php

use Theory\Autoload\Autoloader;

include_once 'src/Autoload/Autoloader.php';

$autoloader = new Autoloader();
$autoloader->addNamespace('Theory', __DIR__ . '/src');
$autoloader->register();

return $autoloader;