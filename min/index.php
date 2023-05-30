<?php

use Minify\App;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/mrclay/minify/lib/Minify/App.php';

error_reporting(E_ERROR);
$app = new App(__DIR__);
$app->runServer();
