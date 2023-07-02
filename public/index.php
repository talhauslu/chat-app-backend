<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$routes = require __DIR__ . '/../src/routes.php';

$routes($app);

$app->run();