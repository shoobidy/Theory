<?php

$root = dirname(__DIR__);

$autoloader = include "$root/autoload.php";
$autoloader->addNamespace('Theory/Tests/Unit', __DIR__ . '/Unit');
$autoloader->addNamespace('Theory/Tests/Integration', __DIR__ . '/Integration'); 