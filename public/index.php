<?php

use Barker\Main\App;

ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->getResponse()->send();
dd($app);
