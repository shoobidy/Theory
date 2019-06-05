<?php

use Theory\Autoload\Autoloader;

require '../src/Autoload/Autoloader.php';

$autoloader = new Autoloader([
    'Theory' => '/www/apps/theory/lib/theory/src',
    'Theory\\Tests' => '/www/apps/theory/lib/theory/tests',
], '');
